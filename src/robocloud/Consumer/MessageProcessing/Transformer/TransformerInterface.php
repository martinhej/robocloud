<?php

namespace robocloud\Consumer\MessageProcessing\Transformer;

use robocloud\Message\MessageInterface;

/**
 * Transforms a message to a form expected by BackendInterface implementation.
 */
interface TransformerInterface
{

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
