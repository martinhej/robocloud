<?php

namespace robocloud\Kinesis;

use Aws\Kinesis\KinesisClient;
use Psr\SimpleCache\CacheInterface;
use robocloud\Consumer\ConsumerInterface;
use robocloud\Event\Kinesis\KinesisConsumerError;
use robocloud\Event\MessagesConsumedEvent;
use robocloud\Exception\ShardInitiationException;
use robocloud\Message\MessageFactoryInterface;
use robocloud\Message\MessageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Consumer.
 *
 * The client to read records from Amazon Kinesis Stream. The main functions of
 * this class are:
 * - provide a way to continue at the last read record
 * - skip Kinesis Stream results having empty Records
 * - convert Kinesis Stream results to objects of the type MessageInterface.
 *
 * @package robocloud\Kinesis
 */
class Consumer extends AbstractKinesisService implements ConsumerInterface
{

    /**
     * The sequence number at which the last reading stopped.
     *
     * @var string
     */
    protected $lastSequenceNumber;

    /**
     * The shard id from which the last reading was done.
     *
     * @var string
     */
    protected $lastShardId;

    /**
     * The count of messages read from the stream at once.
     *
     * @var int
     */
    protected $batchSize = 10;

    /**
     * The initial shard iterator type.
     *
     * Set by default to "TRIM_HORIZON" which means from the beginning. It could
     * be set to "LATEST" that will read most recent data in the shard.
     *
     * @var string
     *
     * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_GetShardIterator.html
     */
    protected $initialShardIteratorType = self::ITERATOR_TYPE_TRIM_HORIZON;

    /**
     * Number of milliseconds behind latest event in the stream.
     *
     * @var int
     */
    protected $lag;

    /**
     * @var ConsumerRecoveryInterface
     */
    protected $consumerRecovery;

    /**
     * Read from beginning iterator type.
     *
     * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_GetShardIterator.html
     */
    const ITERATOR_TYPE_TRIM_HORIZON = 'TRIM_HORIZON';

    /**
     * Read after the provided sequence number iterator type.
     *
     * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_GetShardIterator.html
     */
    const ITERATOR_TYPE_AFTER_SEQUENCE_NUMBER = 'AFTER_SEQUENCE_NUMBER';

    /**
     * Number of microseconds to wait between Kinesis reads.
     */
    const WAIT_UTIME_BEFORE_NEXT_READ = 500;

    /**
     * Consumer constructor.
     *
     * @param KinesisClient $client
     * @param MessageFactoryInterface $message_factory
     * @param EventDispatcherInterface $event_dispatcher
     * @param CacheInterface $cache
     * @param ConsumerRecoveryInterface $consumer_recovery
     */
    public function __construct(KinesisClient $client, MessageFactoryInterface $message_factory, EventDispatcherInterface $event_dispatcher, CacheInterface $cache, ConsumerRecoveryInterface $consumer_recovery)
    {
        $this->consumerRecovery = $consumer_recovery;
        parent::__construct($client, $this->getConsumerRecovery()->getStreamName(), $message_factory, $event_dispatcher, $cache);
    }

    /**
     * {@inheritdoc}
     */
    public function setConsumerData(array $data): ConsumerInterface
    {
        $this->getConsumerRecovery()->setConsumerData($data);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function consume($runTime = 0)
    {
        $shards_ids = $this->getShardIds();
        $shard_id = $this->getConsumerRecovery()->getShardId();
        if (!in_array($shard_id, $shards_ids)) {
            throw new ShardInitiationException('Invalid shard id provided: ' . $shard_id);
        }

        $last_sequence_number = NULL;
        $start = time();

        if ($this->getConsumerRecovery()->hasRecoveryData()) {
            $last_sequence_number = $this->getConsumerRecovery()->getLastSequenceNumber();
        }

        do {
            $messages = $this->getMessages($shard_id, $last_sequence_number, $runTime);

            // Do not bother when we do not receive any records.
            if (!empty($messages)) {
                $last_sequence_number = $this->getLastSequenceNumber();

                try {
                    $this->getEventDispatcher()->dispatch(MessagesConsumedEvent::NAME, new MessagesConsumedEvent($messages));
                } catch (\Exception $e) {
                    $this->processError($e, $messages);
                }

                $this->getConsumerRecovery()->storeLastSuccessPosition($last_sequence_number);
            }
        }
        while ($this->getLag() > 0 && ($start + $runTime > time()));
    }

    /**
     * Do a single call to a Kinesis stream to get messages.
     *
     * @param string $shardId
     *   The shard id from which to start reading.
     * @param string $lastSequenceNumber
     *   The last record sequence number the last call of getRecords() ended. The
     *   value will be provided by a subsequent call of getLastSequenceNumber().
     *
     * @return \robocloud\Message\MessageInterface[]
     *   The messages.
     */
    protected function getMessages($shardId = NULL, $lastSequenceNumber = NULL): array
    {

        $records = [];

        // Get the initial iterator.
        try {
            $shard_iterator = $this->getInitialShardIterator($shardId, $lastSequenceNumber);
        } catch (\Exception $e) {
            $this->processError($e, [
                'shard_id' => $shardId,
                'last_sequence_number' => $lastSequenceNumber,
            ]);

            return $records;
        }

        // Keep running until we have next shard iterator and we are not at
        // the latest record and (we are not over the run time or we have not
        // yet received any records).
        do {

            $has_records = FALSE;

            // Load batch of records.
            try {
                $res = $this->getClient()->getRecords([
                    'Limit' => $this->batchSize,
                    'ShardIterator' => $shard_iterator,
                    'StreamName' => $this->getStreamName(),
                ]);

                // Get the Shard iterator for next batch.
                $shard_iterator = $res->get('NextShardIterator');
                $behind_latest = $res->get('MillisBehindLatest');

                foreach ($res->search('Records[].[SequenceNumber, Data]') as $event) {
                    list($sequence_number, $message_data) = $event;

                    try {
                        $records[$sequence_number] = $this->getMessageFactory()->unserialize($message_data);
                    } catch (\Exception $e) {
                        $this->processError($e, $event);
                    }

                    $has_records = TRUE;
                }
            } catch (\Exception $e) {
                $this->processError($e);
            }

        } while (!empty($shard_iterator) && !empty($behind_latest) && !$has_records);

        if (!empty($sequence_number)) {
            $this->lastSequenceNumber = $sequence_number;
        }

        if (!empty($shardId)) {
            $this->lastShardId = $shardId;
        }

        if (!empty($behind_latest)) {
            $this->lag = $behind_latest;
        }

        return $records;
    }

    /**
     * {@inheritdoc}
     */
    public function getLag(): int
    {
        return $this->lag;
    }

    /**
     * Gets the last sequence number.
     *
     * @return string
     *   Sequence number.
     */
    protected function getLastSequenceNumber(): string
    {
        return $this->lastSequenceNumber;
    }

    /**
     * Gets the last shard id.
     *
     * @return string
     *   The shard id.
     */
    protected function getLastShardId(): string
    {
        return $this->lastShardId;
    }

    /**
     * Gets the initial shard iterator.
     *
     * @param string $shard_id
     *   The shard id.
     * @param string $starting_sequence_number
     *   The starting sequence number.
     *
     * @return object
     *   The shard iterator.
     */
    protected function getInitialShardIterator($shard_id, $starting_sequence_number = NULL)
    {

        $args = [
            'ShardId' => $shard_id,
            'StreamName' => $this->getStreamName(),
            'ShardIteratorType' => $this->initialShardIteratorType,
        ];

        // If we have the starting sequence number update also the ShardIteratorType
        // to start reading just after it.
        if (!empty($starting_sequence_number)) {
            $args['ShardIteratorType'] = self::ITERATOR_TYPE_AFTER_SEQUENCE_NUMBER;
            $args['StartingSequenceNumber'] = $starting_sequence_number;
        }

        $res = $this->getClient()->getShardIterator($args);

        return $res->get('ShardIterator');
    }

    /**
     * @param \Exception $exception
     * @param array $data
     */
    public function processError(\Exception $exception, array $data = [])
    {
        $this->getEventDispatcher()->dispatch(KinesisConsumerError::NAME, new KinesisConsumerError($exception, $data));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'consumer';
    }

    /**
     * @return ConsumerRecoveryInterface
     */
    protected function getConsumerRecovery(): ConsumerRecoveryInterface
    {
        return $this->consumerRecovery;
    }

    /**
     * Gets shard ids.
     *
     * @return int[]
     *   List of shard ids within the stream.
     */
    protected function getShardIds(): array
    {
        $key = $this->getStreamName() . '.shard_ids';

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $res = $this->getClient()->describeStream(['StreamName' => $this->getStreamName()]);
        $shard_ids = $res->search('StreamDescription.Shards[].ShardId');
        // Cache shard_ids for a day as overuse of the describeStream
        // resource results in LimitExceededException error.
        $this->cache->set($key, $shard_ids, 86400);

        return $shard_ids;
    }

}
