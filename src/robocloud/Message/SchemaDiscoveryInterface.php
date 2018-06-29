<?php

namespace robocloud\Message;

use robocloud\Exception\InvalidMessageDataException;

/**
 * Defines the schema discovery.
 */
interface SchemaDiscoveryInterface {

    /**
     * Gets the general message schema.
     *
     * @return object
     *   The general message schema.
     */
    public function getGeneralMessageSchema(): \stdClass;

    /**
     * Gets message schema.
     *
     * @param MessageInterface $message
     *   The message for which to get schema.
     *
     * @return object
     *   The message data property schema.
     *
     * @throws InvalidMessageDataException
     */
    public function getMessageDataSchema(MessageInterface $message): \stdClass;
}
