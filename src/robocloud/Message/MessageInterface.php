<?php

namespace robocloud\Message;

/**
 * Interface MessageInterface.
 *
 * Defines the basic structure for a message object.
 *
 * @package robocloud\Message
 */
interface MessageInterface extends \JsonSerializable {

  /**
   * Low priority flag.
   */
  const PRIORITY_LOW = 'low';

  /**
   * Medium priority flag.
   */
  const PRIORITY_MEDIUM = 'medium';

  /**
   * High priority flag.
   *
   * Messages with this priority will be pushed to the stream immediately.
   * Therefore use this priority level only for deletions or idempotent
   * operations.
   */
  const PRIORITY_HIGH = 'high';

  /**
   * Gets the robot id that emitted the message.
   *
   * Structure: [area].[destination].[purpose].[name]
   *
   * @return string
   *   The robot id.
   */
  public function getRoboId();

  /**
   * Gets the unique identifier of the message.
   *
   * Structure: [roboId].[id].
   *
   * @return string
   *   Namespaced message identification.
   */
  public function getMessageId();

  /**
   * Gets the message purpose.
   *
   * @return string
   *   The message purpose.
   */
  public function getPurpose();

  /**
   * Gets message tags.
   *
   * @return array
   *   Arbitrary set of labels relevant to the specific message.
   */
  public function getTags();

  /**
   * Gets the message time.
   *
   * @return string
   *   The message time.
   */
  public function getMessageTime();

  /**
   * Gets the message data.
   *
   * @return mixed
   *   The message data.
   */
  public function getData();

  /**
   * Gets the message priority.
   *
   * @return string
   *   One of the self::PRIORITY_* constants.
   */
  public function getPriority();

  /**
   * Gets the response to origin.
   *
   * @return string
   *   The origin of the message that this is response to.
   */
  public function getResponseTo();

  /**
   * Gets the list of robo-ids of recipients.
   *
   * @return array
   *   The robo-ids of recipients.
   */
  public function getRecipients();

  /**
   * Gets the recipients wildcard.
   *
   * @return string
   *   The recipients wildcard.
   */
  public function getRecipientWildcard();

}
