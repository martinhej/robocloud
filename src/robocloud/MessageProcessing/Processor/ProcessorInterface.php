<?php

namespace robocloud\MessageProcessing\Processor;

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
   * @param \robocloud\Message\MessageInterface[] $messages
   *   Messages to be processed.
   */
  public function processMessages(array $messages);

}
