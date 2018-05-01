<?php

namespace robocloud\MessageProcessing\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Class JsonTransformer.
 *
 * @package robocloud\MessageProcessing\Transformer
 */
class JsonTransformer implements TransformerInterface {

  /**
   * {@inheritdoc}
   */
  public function transformMessage(MessageInterface $message) {
    return json_encode($message);
  }

}
