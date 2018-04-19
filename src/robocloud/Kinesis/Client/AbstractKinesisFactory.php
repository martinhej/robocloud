<?php

namespace robocloud\Kinesis\Client;

use Aws\Sdk;

/**
 * Class KinesisFactory.
 *
 * @package robocloud\Kinesis\client
 */
abstract class AbstractKinesisFactory implements KinesisFactoryInterface {

  /**
   * The AWS SDK object.
   *
   * @var \Aws\Sdk
   */
  protected $aws;

  /**
   * The event stream name.
   *
   * @var string
   */
  protected $streamName;

  /**
   * The Producer Kinesis client.
   *
   * @var \robocloud\Kinesis\Client\Producer
   */
  protected $producer;

  /**
   * The consumer Kinesis client.
   *
   * @var \robocloud\Kinesis\Client\Consumer
   */
  protected $consumer;

  /**
   * Factory instances.
   *
   * @var \robocloud\Kinesis\Client\AbstractKinesisFactory[]
   */
  protected static $instances;

  /**
   * Gets one instance per stream name.
   *
   * @param string $stream_name
   *   The Kinesis stream name.
   *
   * @return \robocloud\Kinesis\Client\AbstractKinesisFactory
   *   The factory instance.
   */
  public static function getStream($stream_name) {
    if (empty(self::$instances[$stream_name])) {
      $class = get_called_class();
      self::$instances[$stream_name] = new $class(new Sdk(), $stream_name);
    }

    return self::$instances[$stream_name];
  }

  /**
   * AbstractKinesisFactory constructor.
   *
   * @param \Aws\Sdk $aws_sdk
   *   The AWS SDK object.
   * @param string $stream_name
   *   The Kinesis stream name.
   */
  public function __construct(Sdk $aws_sdk, $stream_name) {
    $this->aws = $aws_sdk;
    $this->streamName = $stream_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getConsumer(array $config = []) {

    if (empty($this->consumer)) {
      $client = $this->getAwsSdk()->createKinesis($this->getConfig('consumer'));
      $this->consumer = new Consumer($client, $this->getStreamName(), $config);
    }

    return $this->consumer;
  }

  /**
   * {@inheritdoc}
   */
  public function getProducer(array $config = []) {

    if (empty($this->producer)) {
      $client = $this->getAwsSdk()->createKinesis($this->getConfig('producer'));
      $this->producer = new Producer($client, $this->getStreamName(), $config);
    }

    return $this->producer;
  }

  /**
   * Gets the AWS SDK.
   *
   * @return \Aws\Sdk
   *   The Sdk object.
   */
  public function getAwsSdk() {
    return $this->aws;
  }

  /**
   * Gets the event stream name.
   *
   * @return string
   *   Event stream name.
   */
  public function getStreamName() {
    return $this->streamName;
  }

  /**
   * Gets the Kinesis configuration.
   *
   * @param string $type
   *   The client type [consumer, producer].
   *
   * @return array
   *   Kinesis configuration.
   */
  abstract public function getConfig($type);

}
