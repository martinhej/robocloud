<?php

namespace robocloud\Event;

use robocloud\Message\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class MessageComposedEvent extends Event {

  const NAME = 'robocloud.message.composed';

  /**
   * @var MessageInterface
   */
  protected $message;

  public function __construct(MessageInterface $message) {
    $this->message = $message;
  }

  /**
   * @return MessageInterface
   */
  public function getMessage() {
    return $this->message;
  }

}
