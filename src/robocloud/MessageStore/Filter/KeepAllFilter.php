<?php

namespace robocloud\MessageStore\Filter;

use robocloud\Message\MessageInterface;

/**
 * Class KeepAllFilter.
 *
 * @package robocloud\MessageStore\Filter
 */
class KeepAllFilter implements FilterInterface {

  /**
   * {@inheritdoc}
   */
  public function keepMessage(MessageInterface $message) {
    return TRUE;
  }

}
