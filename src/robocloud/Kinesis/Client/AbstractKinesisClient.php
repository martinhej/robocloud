<?php

namespace robocloud\Kinesis\Client;

use robocloud\Message\MessageFactoryInterface;
use Aws\Kinesis\KinesisClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
   * @var MessageFactoryInterface
   */
  protected $messageFactory;

  /**
   * @var EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * AbstractClient constructor.
   *
   * @param \Aws\Kinesis\KinesisClient $client
   *   The Kinesis client object.
   * @param string $streamName
   *   The stream name.
   * @param MessageFactoryInterface $messageFactory
   * @param EventDispatcherInterface $eventDispatcher
   */
  public function __construct(KinesisClient $client, $streamName, MessageFactoryInterface $messageFactory, EventDispatcherInterface $eventDispatcher) {
    $this->client = $client;
    $this->streamName = $streamName;
    $this->messageFactory = $messageFactory;
    $this->eventDispatcher = $eventDispatcher;
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
   * Gets the message factory.
   *
   * @return \robocloud\Message\MessageFactoryInterface
   *   The message factory.
   */
  public function getMessageFactory() {
    return $this->messageFactory;
  }

  /**
   * @return EventDispatcherInterface
   */
  protected function getEventDispatcher() {
    return $this->eventDispatcher;
  }

}
