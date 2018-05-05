<?php

namespace robocloud\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DynamoDbInMemoryErrorLogger implements EventSubscriberInterface {

  protected $errors = [];

  /**
   * @param DynamoDbError $error
   */
  public function processError(DynamoDbError $error) {
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
      DynamoDbError::NAME => 'processError',
    ];
  }

  public function getErrors() {
    return $this->errors;
  }

}
