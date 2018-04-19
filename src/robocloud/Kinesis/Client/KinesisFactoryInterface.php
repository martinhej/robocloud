<?php

namespace robocloud\Kinesis\Client;

/**
 * Interface KinesisFactoryInterface.
 *
 * @package robocloud\Kinesis\Client
 */
interface KinesisFactoryInterface {

  /**
   * Gets the Kinesis Consumer client.
   *
   * @param array $config
   *   Config array needed to instantiate Consumer object.
   *
   * @return \robocloud\Kinesis\Client\Consumer
   *   The Consumer client object.
   */
  public function getConsumer(array $config = []);

  /**
   * Gets the Kinesis Producer client.
   *
   * @param array $config
   *   Config array needed to instantiate Consumer object
   *
   * @return \robocloud\Kinesis\Client\Producer
   *   The Producer client object.
   */
  public function getProducer(array $config = []);

  /**
   * Gets the Kinesis stream name.
   *
   * @return string
   *   The stream name.
   */
  public function getStreamName();

}
