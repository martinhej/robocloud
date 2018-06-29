<?php

namespace robocloud\Message;

/**
 * Interface MessageFactoryInterface.
 *
 * @package robocloud\Message
 */
interface MessageFactoryInterface
{

    /**
     * Message data to be used upon creation.
     *
     * @param array $data
     *   The message data.
     *
     * @return $this
     */
    public function setMessageData(array $data): MessageFactoryInterface;

    /**
     * Creates the message instance.
     *
     * @return \robocloud\Message\MessageInterface
     *   The message instance.
     *
     * @throws \robocloud\Exception\InvalidMessageDataException
     *   If a valid message could not be created from the provided data.
     */
    public function createMessage(): MessageInterface;

    /**
     * Serializes the message into format to be pushed into the Kinesis stream.
     *
     * @param \robocloud\Message\MessageInterface $message
     *   The message object to be serialized.
     *
     * @return mixed
     *   The serialization result.
     */
    public function serialize(MessageInterface $message);

    /**
     * Unserializes the serialized message read from the Kinesis stream.
     *
     * @param mixed $serialized_message
     *   The serialized message.
     *
     * @return \robocloud\Message\MessageInterface
     *   The message object.
     *
     * @throws \InvalidArgumentException
     *   When the serialized message could not be unserialized.
     */
    public function unserialize($serialized_message): MessageInterface;

}
