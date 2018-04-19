<?php

namespace robocloud\MessageProcessing\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Interface TransformerInterface.
 *
 * Acts as a bridge between robocloud and the BackendProcessor.
 *
 * @package robocloud\MessageProcessing\Transformer
 */
interface TransformerInterface {

  /**
   * Should transform the provided message to what the backend expects.
   *
   * @param \robocloud\Message\MessageInterface $message
   *   The message to be transformed.
   *
   * @return mixed
   *   Whatever the used storage is expecting for a single item to be written.
   */
  public function transformMessage(MessageInterface $message);

}
