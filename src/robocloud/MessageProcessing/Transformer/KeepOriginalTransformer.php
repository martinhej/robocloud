<?php

namespace robocloud\MessageProcessing\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Class KeepOriginalTransformer.
 *
 * @package robocloud\MessageProcessing\Transformer
 */
class KeepOriginalTransformer implements TransformerInterface {

  /**
   * {@inheritdoc}
   */
  public function transformMessage(MessageInterface $message) {
    return $message;
  }

}
