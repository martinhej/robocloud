<?php

namespace robocloud\Kinesis\Client;

use robocloud\Message\MessageFactoryInterface;
use Aws\Kinesis\KinesisClient;

/**
 * Class AbstractKinesisClient.
 *
 * @package robocloud\Kinesis
 */
abstract class AbstractKinesisClient implements ClientInterface {

  /**
   * The Kinesis client object.
   *
   * @var \Aws\Kinesis\KinesisClient
   */
  protected $client;

  /**
   * The stream name.
   *
   * @var string
   */
  protected $streamName;

  /**
   * Config array.
   *
   * @var array
   */
  protected $config = [];

  /**
   * AbstractClient constructor.
   *
   * @param \Aws\Kinesis\KinesisClient $client
   *   The Kinesis client object.
   * @param string $stream_name
   *   The stream name.
   * @param array $config
   *   - message_factory : The message factory object used to create messages.
   *   - error : Executed if an error was encountered interacting with Kinesis
   *     service.
   */
  public function __construct(KinesisClient $client, $stream_name, array $config) {
    $this->client = $client;
    $this->streamName = $stream_name;
    $this->config = $config;

    if (isset($this->config['error']) && !is_callable($this->config['error'])) {
      throw new \InvalidArgumentException('Argument "error" in the config array must be a callable.');
    }

    if (!isset($this->config['message_factory']) || !is_a($this->config['message_factory'], MessageFactoryInterface::class)) {
      throw new \InvalidArgumentException('Message factory not provided');
    }
  }

  /**
   * Gets the stream name.
   *
   * @return string
   *   The stream name.
   */
  public function getStreamName() {
    return $this->streamName;
  }

  /**
   * Gets the Kinesis client.
   *
   * @return \Aws\Kinesis\KinesisClient
   *   The Kinesis client.
   */
  public function getClient() {
    return $this->client;
  }

  /**
   * Gets shard ids.
   *
   * @return int[]
   *   List of shard ids within the stream.
   */
  public function getShardIds() {
    $res = $this->getClient()->describeStream(['StreamName' => $this->getStreamName()]);
    return $res->search('StreamDescription.Shards[].ShardId');
  }

  /**
   * Processes an error.
   *
   * @param \Exception $exception
   *   The exception.
   * @param mixed $data
   *   Additional data involved in the error.
   */
  public function processError(\Exception $exception, $data = NULL) {
    if (isset($this->config['error'])) {
      $error = $this->config['error'];
      $error($exception, $data);
    }
  }

  /**
   * Gets the message factory.
   *
   * @return \robocloud\Message\MessageFactoryInterface
   *   The message factory.
   */
  public function getMessageFactory() {
    return $this->config['message_factory'];
  }

}
