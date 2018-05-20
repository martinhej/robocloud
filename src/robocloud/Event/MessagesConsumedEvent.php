<?php

namespace robocloud\Event;

use robocloud\Message\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MessagesConsumedEvent.
 *
 * @package robocloud\Event
 */
class MessagesConsumedEvent extends Event
{

    const NAME = 'robocloud.kinesis_consumer.messages_consumed';

    /**
     * @var MessageInterface[]
     */
    protected $messages = [];

    /**
     * MessagesConsumedEvent constructor.
     *
     * @param array $messages
     */
    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return MessageInterface[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

}
