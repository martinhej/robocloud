<?php

namespace robocloud\Kinesis\Client;

use robocloud\Message\MessageInterface;
use Aws\Kinesis\KinesisClient;

/**
 * Class Producer.
 *
 * The client to push data to Kinesis Stream. It provides simple buffer so that
 * messages are pushed to the stream in single call.
 *
 * @package robocloud\Kinesis
 */
class Producer extends AbstractKinesisClient implements ProducerInterface {

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
   * Producer constructor.
   *
   * @param \Aws\Kinesis\KinesisClient $client
   *   The Kinesis client object.
   * @param string $stream_name
   *   The stream name.
   * @param array $config
   *   - error : Executed if an error was encountered executing a,
   *     putRecords operation, otherwise errors are ignored. It should
   *     accept an \Exception and an array of MessageInterface objects as its
   *     arguments.
   *   - batch_size : Count of the records that will be pushed to Kinesis
   *     stream in single request. The max batch size is 500.
   *
   * @throws \InvalidArgumentException
   *   When invalid configuration options are provided.
   */
  public function __construct(KinesisClient $client, $stream_name, array $config) {
    parent::__construct($client, $stream_name, $config);

    if ($this->getBatchSize() > self::KINESIS_MAX_PUT_RECORDS_COUNT) {
      throw new \InvalidArgumentException('The records count per requests exceeds the max put records count');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function add(MessageInterface $message) {
    $this->buffer[] = $message;
  }

  /**
   * {@inheritdoc}
   */
  public function pushAll() {
    $args = ['StreamName' => $this->getStreamName()];
    $result = [];

    foreach (array_chunk($this->buffer, $this->getBatchSize()) as $chunk) {
      /** @var \robocloud\Message\Message $message */
      foreach ($chunk as $message) {
        $args['Records'][] = [
          'Data' => $this->getMessageFactory()->serialize($message),
          'PartitionKey' => $message->getRoboId(),
        ];
      }

      if (!empty($args['Records'])) {
        try {
          $result[] = $this->getClient()->putRecords($args);
        }
        catch (\Exception $e) {
          if (isset($this->config['error'])) {
            $error = $this->config['error'];
            $error($e, $chunk);
          }
        }
      }

    }

    // Empty the buffer.
    $this->buffer = [];

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getBatchSize() {
    if (isset($this->config['batch_size'])) {
      return $this->config['batch_size'];
    }

    return self::KINESIS_MAX_PUT_RECORDS_COUNT;
  }

}
