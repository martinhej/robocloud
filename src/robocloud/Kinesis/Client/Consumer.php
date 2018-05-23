<?php

namespace robocloud\Kinesis\Client;

use Aws\Kinesis\KinesisClient;
use Psr\SimpleCache\CacheInterface;
use robocloud\Event\KinesisConsumerError;
use robocloud\Event\MessagesConsumedEvent;
use robocloud\Exception\ShardInitiationException;
use robocloud\Message\MessageFactoryInterface;
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
class Consumer extends AbstractKinesisClient implements ConsumerInterface
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
     * @param string $streamName
     * @param MessageFactoryInterface $messageFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param CacheInterface $cache
     * @param ConsumerRecoveryInterface $consumerRecovery
     */
    public function __construct(KinesisClient $client, $streamName, MessageFactoryInterface $messageFactory, EventDispatcherInterface $eventDispatcher, CacheInterface $cache, ConsumerRecoveryInterface $consumerRecovery)
    {
        parent::__construct($client, $streamName, $messageFactory, $eventDispatcher, $cache);

        $this->consumerRecovery = $consumerRecovery;
    }

    /**
     * Consumes messages from Kinesis.
     *
     * @param int $shardPosition
     * @param int $runTime
     *   Time period in seconds for how long the consume process should run.
     *
     * @throws \robocloud\Exception\ShardInitiationException
     */
    public function consume($shardPosition, $runTime = 0)
    {
        $shards_ids = $this->getShardIds();
        if (!isset($shards_ids[$shardPosition])) {
            throw new ShardInitiationException('No shard found at position ' . $shardPosition);
        }

        $shard_id = $shards_ids[$shardPosition];
        $last_sequence_number = NULL;

        if ($this->getConsumerRecovery()->hasRecoveryData()) {
            $last_sequence_number = $this->getConsumerRecovery()->getLastSequenceNumber($shard_id);
        }

        $start = time();

        do {
            $messages = $this->getMessages($shard_id, $last_sequence_number);

            // Do not bother when we do not receive any records.
            if (!empty($messages)) {
                $last_sequence_number = $this->getLastSequenceNumber();

                try {
                    $this->getEventDispatcher()->dispatch(MessagesConsumedEvent::NAME, new MessagesConsumedEvent($messages));
                } catch (\Exception $e) {
                    $this->processError($e, $messages);
                }

                $this->getConsumerRecovery()->storeLastSuccessPosition($this->getLastShardId(), $last_sequence_number);
            }

            usleep(self::WAIT_UTIME_BEFORE_NEXT_READ);

        } while (!empty($messages) && ($start + $runTime) > time());
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages($shardId = NULL, $lastSequenceNumber = NULL)
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
    public function getLag()
    {
        return $this->lag;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastSequenceNumber()
    {
        return $this->lastSequenceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastShardId()
    {
        return $this->lastShardId;
    }

    /**
     * {@inheritdoc}
     */
    public function setBatchSize($size)
    {
        $this->batchSize = (int)$size;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setInitialShardIteratorType($type)
    {
        $this->initialShardIteratorType = $type;
        return $this;
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
     * @return ConsumerRecoveryInterface
     */
    protected function getConsumerRecovery()
    {
        return $this->consumerRecovery;
    }

}
