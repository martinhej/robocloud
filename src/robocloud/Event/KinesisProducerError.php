<?php

namespace robocloud\Event;

use robocloud\Message\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class KinesisProducerError extends Event {

  const NAME = 'robocloud.kinesis_producer.error';

  /**
   * @var \Exception
   */
  protected $exception;

  /**
   * @var MessageInterface[]
   */
  protected $messages = [];

  public function __construct(\Exception $e, array $messages) {
    $this->exception = $e;
    $this->messages = $messages;
  }

  /**
   * @return \Exception
   */
  public function getException() {
    return $this->exception;
  }

  /**
   * @return MessageInterface[]
   */
  public function getMessages() {
    return $this->messages;
  }

}
