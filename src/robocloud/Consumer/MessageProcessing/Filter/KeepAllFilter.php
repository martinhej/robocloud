<?php

namespace robocloud\Consumer\MessageProcessing\Filter;

use robocloud\Message\MessageInterface;

/**
 * Keeps all messages.
 */
class KeepAllFilter implements FilterInterface
{

    /**
     * {@inheritdoc}
     */
    public function keepMessage(MessageInterface $message) : bool
    {
        return TRUE;
    }

}
