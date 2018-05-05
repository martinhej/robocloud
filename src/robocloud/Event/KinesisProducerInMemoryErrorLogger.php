<?php

namespace robocloud\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KinesisProducerInMemoryErrorLogger implements EventSubscriberInterface {

  protected $errors = [];

  /**
   * @param KinesisProducerError $error
   */
  public function processError(KinesisProducerError $error) {
    $this->errors[] = [
      'message' => $error->getException()->getMessage(),
      'exception' => $error->getException(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KinesisProducerError::NAME => 'processError',
    ];
  }

  public function getErrors() {
    return $this->errors;
  }

}
