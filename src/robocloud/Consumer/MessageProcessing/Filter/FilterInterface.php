<?php

namespace robocloud\Consumer\MessageProcessing\Filter;

use robocloud\Message\MessageInterface;

/**
 * Allows in only messages that are expected by the used Backend.
 */
interface FilterInterface
{

    /**
     * Decides if the provided message should be kept or not.
     *
     * @param \robocloud\Message\MessageInterface $message
     *   Message object from a stream.
     *
     * @return bool
     *   TRUE if the message should be kept, FALSE otherwise.
     */
    public function keepMessage(MessageInterface $message) : bool;

}
