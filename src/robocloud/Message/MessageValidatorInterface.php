<?php

namespace robocloud\Message;

use robocloud\Event\MessageComposedEvent;

/**
 * Interface MessageValidatorInterface.
 *
 * @package robocloud\Message
 */
interface MessageValidatorInterface
{

    /**
     * Validates message.
     *
     * @param MessageComposedEvent $event
     *
     * @throws \robocloud\Exception\InvalidMessageDataException
     *   When validation fails.
     */
    public function validate(MessageComposedEvent $event);

}
