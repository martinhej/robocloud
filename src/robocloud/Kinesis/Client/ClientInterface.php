<?php

namespace robocloud\Kinesis\Client;

/**
 * Interface ClientInterface.
 *
 * @package robocloud\Kinesis\Client
 */
interface ClientInterface {

  /**
   * Gets the stream name.
   *
   * @return string
   *   The stream name.
   */
  public function getStreamName();

  /**
   * Gets the Kinesis client.
   *
   * @return \Aws\Kinesis\KinesisClient
   *   The Kinesis client.
   */
  public function getClient();

  /**
   * Gets shard ids.
   *
   * @return int[]
   *   List of shard ids within the stream.
   */
  public function getShardIds();

  /**
   * Processes an error.
   *
   * @param \Exception $exception
   *   The exception.
   * @param mixed $data
   *   Additional data involved in the error.
   */
  public function processError(\Exception $exception, $data = NULL);

  /**
   * Gets the message factory.
   *
   * @return \robocloud\Message\MessageFactoryInterface
   *   The message factory.
   */
  public function getMessageFactory();

}
