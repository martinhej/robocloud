<?php

namespace robocloud\MessageStore\Processor;

/**
 * Interface ProcessorInterface.
 *
 * @package robocloud\MessageStore\Processor
 */
interface ProcessorInterface {

  /**
   * Filters, transforms and writes the provided messages.
   *
   * @param \robocloud\Message\MessageInterface[] $messages
   *   Messages to be processed.
   *
   * @return mixed
   *   The storage response.
   */
  public function processMessages(array $messages);

}
