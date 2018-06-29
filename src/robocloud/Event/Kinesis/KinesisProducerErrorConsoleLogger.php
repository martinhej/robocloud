<?php

namespace robocloud\Event\Kinesis;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class KinesisProducerErrorProcessor.
 *
 * @package robocloud\Event\Kinesis
 */
class KinesisProducerErrorConsoleLogger implements EventSubscriberInterface {

  /**
   * @param KinesisProducerError $error
   */
  public function processError(KinesisProducerError $error) {
    var_dump('=== Kinesis Producer ERROR ===', $error->getException()->getMessage(), '=== ERROR END ===');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KinesisProducerError::NAME => 'processError',
    ];
  }

}
