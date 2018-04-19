<?php

namespace robocloud\MessageStore\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Interface TransformerInterface.
 *
 * @package robocloud\MessageStore\Transformer
 */
interface TransformerInterface {

  /**
   * Should transform the provided message to whatever used storage expects.
   *
   * @param \robocloud\Message\MessageInterface $message
   *   The message to be transformed.
   *
   * @return mixed
   *   Whatever the used storage is expecting for a single item to be written.
   */
  public function transformMessage(MessageInterface $message);

}
