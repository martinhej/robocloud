<?php

namespace robocloud\Event;

use robocloud\Message\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MessageComposedEvent.
 *
 * @package robocloud\Event
 */
class MessageComposedEvent extends Event {

  const NAME = 'robocloud.message.composed';

  /**
   * @var MessageInterface
   */
  protected $message;

    /**
     * MessageComposedEvent constructor.
     *
     * @param MessageInterface $message
     */
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
