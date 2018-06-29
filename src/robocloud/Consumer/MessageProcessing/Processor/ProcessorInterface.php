<?php

namespace robocloud\Consumer\MessageProcessing\Processor;

use robocloud\Event\MessagesConsumedEvent;

/**
 * Puts together the Filter, Transformer and Backend instances to process
 * messages received from robocloud.
 */
interface ProcessorInterface
{

    /**
     * Filters, transforms and writes the provided messages.
     *
     * @param MessagesConsumedEvent $event
     */
    public function processMessages(MessagesConsumedEvent $event);

}
