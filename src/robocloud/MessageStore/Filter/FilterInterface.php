<?php

namespace robocloud\MessageStore\Filter;

use robocloud\Message\MessageInterface;

/**
 * Interface FilterInterface.
 *
 * @package robocloud\MessageStore\Filter
 */
interface FilterInterface {

  /**
   * Decides if the provided message should be kept or not.
   *
   * @param \robocloud\Message\MessageInterface $message
   *   Message object from a stream.
   *
   * @return bool
   *   TRUE if the message should be kept, FALSE otherwise.
   */
  public function keepMessage(MessageInterface $message);

}
