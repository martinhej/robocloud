<?php

namespace robocloud\Kinesis\Client;

use robocloud\Message\MessageInterface;

/**
 * Interface ProducerInterface.
 *
 * @package robocloud\Kinesis\Client
 */
interface ProducerInterface {

  /**
   * Adds message to the buffer.
   *
   * @param \robocloud\Message\MessageInterface $message
   *   The message object.
   */
  public function add(MessageInterface $message);

  /**
   * Pushes all messages in the buffer to the stream.
   *
   * @return \Aws\Result[]
   *   The Kinesis API results.
   *
   * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_PutRecords.html
   */
  public function pushAll();

}
