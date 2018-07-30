<?php

namespace robocloud\Kinesis;

use Aws\Result;
use robocloud\Event\Kinesis\KinesisProducerError;
use robocloud\Exception\KinesisFailedRecordsException;
use robocloud\Message\MessageInterface;
use robocloud\Producer\ProducerInterface;

/**
 * Class Producer.
 *
 * The client to push data to Kinesis Stream. It provides simple buffer so that
 * messages are pushed to the stream in single call.
 *
 * @package robocloud\Kinesis
 */
class Producer extends AbstractKinesisService implements ProducerInterface
{

    /**
     * Message buffer.
     *
     * @var \robocloud\Message\MessageInterface[]
     */
    protected $buffer;

    /**
     * The maximum allowed records count.
     *
     * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_PutRecords.html
     */
    const KINESIS_MAX_PUT_RECORDS_COUNT = 500;

    /**
     * {@inheritdoc}
     */
    public function setProducerData(array $data): ProducerInterface
    {
        // TODO: Implement setProducerData() method.
    }

    /**
     * {@inheritdoc}
     */
    public function add(MessageInterface $message)
    {
        $this->buffer[] = $message;
    }

    /**
     * Pushes all messages from the buffer to the stream.
     *
     * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_PutRecords.html
     */
    public function pushAll()
    {
        /** @var \robocloud\Message\RoboMessage[] $chunk */
        foreach (array_chunk($this->buffer, self::KINESIS_MAX_PUT_RECORDS_COUNT) as $chunk) {
            try {
                $this->putRecords($chunk);
            } catch (\Exception $e) {
                $this->processError($e, $chunk);
            }
        }

        // Empty the buffer.
        $this->buffer = [];
    }

    /**
     * {@inheritdoc}
     */
    public function processError(\Exception $exception, array $messages = [])
    {
        $this->getEventDispatcher()->dispatch(KinesisProducerError::NAME, new KinesisProducerError($exception, $messages));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'producer';
    }

    /**
     * @param array $chunk
     *
     * @throws KinesisFailedRecordsException
     *   When the Kinesis Result FailedRecordCount is grater than zero.
     */
    protected function putRecords(array $chunk)
    {

        $args = ['StreamName' => $this->getStreamName()];

        foreach ($chunk as $message) {
            $args['Records'][] = [
                'Data' => $this->getMessageFactory()->serialize($message),
                // The partition key is the robo id so that from an individual robot
                // messages are pushed into same shard.
                'PartitionKey' => $message->getRoboId(),
            ];
        }

        if (!empty($args['Records'])) {
            /** @var Result $result */
            $result = $this->getClient()->putRecords($args);
            if ($result->get('FailedRecordCount') > 0) {
                throw new KinesisFailedRecordsException('Failed to push [' . $result->get('FailedRecordCount') . '] records');
            }
        }
    }

}
