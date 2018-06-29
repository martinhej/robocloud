<?php

namespace robocloud\Consumer\MessageProcessing\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Transforms messages to JSON.
 */
class JsonTransformer implements TransformerInterface
{

    /**
     * {@inheritdoc}
     */
    public function transformMessage(MessageInterface $message)
    {
        return json_encode($message);
    }

}
