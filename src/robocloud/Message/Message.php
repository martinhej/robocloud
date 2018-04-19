<?php

namespace robocloud\Message;

/**
 * Class Message.
 *
 * @package robocloud\Message
 */
class Message implements MessageInterface {

  /**
   * The message ID.
   *
   * @var string
   */
  public $messageId;

  /**
   * The message related object identifier.
   *
   * @var string
   */
  public $purpose;

  /**
   * The message partition key.
   *
   * @var string
   */
  public $roboId;

  /**
   * The message priority set to medium by default.
   *
   * @var string
   */
  public $priority = self::PRIORITY_MEDIUM;

  /**
   * The message labels.
   *
   * @var array
   */
  public $tags = [];

  /**
   * The message time.
   *
   * @var string
   */
  public $messageTime;

  /**
   * Additional message data.
   *
   * @var array
   */
  public $data = [];

  public $responseTo;

  public $recipients = [];

  public $recipientsWildcard;

  /**
   * Message constructor.
   *
   * @param array $message_data
   *   The message data necessary to initiate a message instance.
   */
  public function __construct(array $message_data) {
    foreach ($message_data as $key => $value) {
      if (property_exists(get_called_class(), $key)) {
        $this->{$key} = $value;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRoboId() {
    return $this->roboId;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageId() {
    return $this->messageId;
  }

  /**
   * {@inheritdoc}
   */
  public function getPurpose() {
    return $this->purpose;
  }

  /**
   * {@inheritdoc}
   */
  public function getPriority() {
    return $this->priority;
  }

  /**
   * {@inheritdoc}
   */
  public function getTags() {
    return $this->tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageTime() {
    return $this->messageTime;
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {
    return $this->data;
  }

  /**
   * {@inheritdoc}
   */
  public function getResponseTo() {
    return $this->responseTo;
  }

  /**
   * {@inheritdoc}
   */
  public function getRecipients() {
    return $this->recipients;
  }

  /**
   * {@inheritdoc}
   */
  public function getRecipientWildcard() {
    return $this->recipientsWildcard;
  }

  /**
   * {@inheritdoc}
   */
  public function jsonSerialize() {
    return [
      'origin' => $this->getMessageId(),
      'roboId' => $this->getRoboId(),
      'priority' => $this->getPriority(),
      'purpose' => $this->getPurpose(),
      'tags' => $this->getTags(),
      'messageTime' => $this->getMessageTime(),
      'data' => $this->getData(),
      'responseTo' => $this->getResponseTo(),
      'recipients' => $this->getRecipients(),
      'recipientWildcard' => $this->getRecipientWildcard(),
    ];
  }

  /**
   * Sets the message origin.
   *
   * @param string $messageId
   *   The message origin.
   *
   * @return $this
   */
  public function setMessageId($messageId) {
    $this->messageId = $messageId;
    return $this;
  }

  /**
   * Sets the message partition key.
   *
   * @param string $roboId
   *   The partition key.
   *
   * @return $this
   */
  public function setRoboId($roboId) {
    $this->roboId = $roboId;
    return $this;
  }

  /**
   * Sets the message priority.
   *
   * @param string $priority
   *   The message priority.
   *
   * @return $this
   */
  public function setPriority($priority) {
    $this->priority = $priority;
    return $this;
  }

  /**
   * Sets labels.
   *
   * @param array $tags
   *   The labels to be set.
   *
   * @return $this
   */
  public function setTags(array $tags) {
    $this->tags = $tags;
    return $this;
  }

  /**
   * Sets the event time.
   *
   * @param string $messageTime
   *   The event time in the ISO format Y-m-dTH:i:s.
   *
   * @return $this
   */
  public function setMessageTime($messageTime) {
    $this->messageTime = $messageTime;
    return $this;
  }

  /**
   * Sets message data property.
   *
   * @param mixed $data
   *   The data to be set.
   *
   * @return $this
   */
  public function setData($data) {
    $this->data = $data;
    return $this;
  }

}
