<?php

namespace robocloud\Event;

use robocloud\Message\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class KinesisConsumerError extends Event {

  const NAME = 'robocloud.kinesis_consumer.error';

  /**
   * @var \Exception
   */
  protected $exception;

  /**
   * @var array
   */
  protected $data = [];

  public function __construct(\Exception $e, array $data = []) {
    $this->exception = $e;
    $this->data = $data;
  }

  /**
   * @return \Exception
   */
  public function getException() {
    return $this->exception;
  }

  /**
   * @return array
   */
  public function getData() {
    return $this->data;
  }

}
