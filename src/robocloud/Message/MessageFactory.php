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
class MessageFactory implements MessageFactoryInterface {

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
   * @param EventDispatcherInterface $eventDispatcher
   *   The event dispatcher object.
   */
  public function __construct(EventDispatcherInterface $eventDispatcher) {
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public function serialize(MessageInterface $message) {
    return json_encode($message);
  }

  /**
   * {@inheritdoc}
   */
  public function unserialize($serialized_message) {
    $result = json_decode($serialized_message, TRUE);

    if (empty($result) || is_string($result)) {
      throw new \InvalidArgumentException('Could not unserialize the message data');
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessageClass($class) {
    $this->messageClass = $class;

    if (!in_array(MessageInterface::class, class_implements($this->messageClass))) {
      throw new InvalidMessageClassException('Invalid message class provided: ' . $this->messageClass);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setMessageData(array $data) {
    $this->messageData = $data;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function createMessage() {
    /** @var \robocloud\Message\MessageInterface $message */
    $message = new $this->messageClass($this->getData());

    $messageComposedEvent = new MessageComposedEvent($message);
    $this->eventDispatcher->dispatch(MessageComposedEvent::NAME, $messageComposedEvent);

    return $message;
  }

  /**
   * Gets the data using which the message object will be instantiated.
   *
   * @return array
   *   The message data.
   */
  public function getData() {
    return $this->messageData;
  }

}
