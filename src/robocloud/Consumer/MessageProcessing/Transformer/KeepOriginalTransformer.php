<?php

namespace robocloud\Consumer\MessageProcessing\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Will not do any transformation.
 */
class KeepOriginalTransformer implements TransformerInterface
{

    /**
     * {@inheritdoc}
     */
    public function transformMessage(MessageInterface $message)
    {
        return $message;
    }

}
