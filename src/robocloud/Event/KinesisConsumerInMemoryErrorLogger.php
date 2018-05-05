<?php

namespace robocloud\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KinesisConsumerInMemoryErrorLogger implements EventSubscriberInterface {

  protected $errors = [];

  /**
   * @param KinesisConsumerError $error
   */
  public function processError(KinesisConsumerError $error) {
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
      KinesisConsumerError::NAME => 'processError',
    ];
  }

  public function getErrors() {
    return $this->errors;
  }

}
