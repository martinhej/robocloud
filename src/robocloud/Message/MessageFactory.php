<?php

namespace robocloud\Message;

use robocloud\Config\ConfigInterface;
use robocloud\Event\MessageComposedEvent;
use robocloud\Exception\InvalidMessageClassException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class MessageFactory.
 *
 * @package robocloud\Message
 */
class MessageFactory implements MessageFactoryInterface
{

    /**
     * The message data to be used to create message.
     *
     * @var array
     */
    protected $messageData = [];

    /**
     * The message class to be instantiated.
     *
     * @var string
     */
    protected $messageClass;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * MessageFactory constructor.
     *
     * @param string $message_class
     *   The message class.
     * @param EventDispatcherInterface $event_dispatcher
     *   The event dispatcher object.
     */
    public function __construct($message_class, EventDispatcherInterface $event_dispatcher)
    {
        $this->messageClass = $message_class;
        $this->eventDispatcher = $event_dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(MessageInterface $message)
    {
        return json_encode($message);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized_message): MessageInterface
    {
        $result = json_decode($serialized_message, TRUE);

        if (empty($result) || is_string($result)) {
            throw new \InvalidArgumentException('Could not unserialize the message data');
        }

        return $this->setMessageData($result)->createMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageData(array $data): MessageFactoryInterface
    {
        $this->messageData = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): MessageInterface
    {

        if (!is_string($this->messageClass) || !in_array(MessageInterface::class, class_implements($this->messageClass))) {
            throw new InvalidMessageClassException('Invalid message class provided: ' . $this->messageClass);
        }

        /** @var \robocloud\Message\MessageInterface $message */
        $message = new $this->messageClass($this->messageData);
        $messageComposedEvent = new MessageComposedEvent($message);
        $this->eventDispatcher->dispatch(MessageComposedEvent::NAME, $messageComposedEvent);

        return $message;
    }

}
