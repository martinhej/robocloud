<?php

namespace robocloud\Producer;

use robocloud\Message\MessageInterface;

/**
 * Interface ProducerInterface.
 *
 * @package robocloud\Producer
 */
interface ProducerInterface
{

    /**
     * Sets producer data needed for proper function.
     *
     * @param array $data
     *   The producer data.
     *
     * @return ProducerInterface
     */
    public function setProducerData(array $data): ProducerInterface;

    /**
     * Adds message to the buffer.
     *
     * @param \robocloud\Message\MessageInterface $message
     *   The message object.
     */
    public function add(MessageInterface $message);

    /**
     * Pushes all messages from the buffer to the stream.
     */
    public function pushAll();

}
