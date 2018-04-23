<?php

namespace robocloud\MessageProcessing\Processor;
use robocloud\Event\MessagesConsumedEvent;

/**
 * Interface ProcessorInterface.
 *
 * Puts together the Filter, Transformer and Backend instances to process
 * messages received from robocloud.
 *
 * @package robocloud\MessageProcessing\Processor
 */
interface ProcessorInterface {

  /**
   * Filters, transforms and writes the provided messages.
   *
   * @param MessagesConsumedEvent $event
   */
  public function processMessages(MessagesConsumedEvent $event);

}
