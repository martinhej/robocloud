<?php

namespace robocloud\Message;

/**
 * Interface MessageInterface.
 *
 * Defines the basic structure for a message object.
 *
 * @package robocloud\Message
 */
interface MessageInterface extends \JsonSerializable
{

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
     */
    const PRIORITY_HIGH = 'high';

    /**
     * Gets the message version.
     *
     * @return string
     *   The message version.
     */
    public function getVersion(): string;

    /**
     * Gets message tags.
     *
     * @return array
     *   Arbitrary set of labels relevant to the specific message.
     */
    public function getTags(): ?array;

    /**
     * Gets the message time.
     *
     * @return string
     *   The message time.
     */
    public function getMessageTime(): string;

    /**
     * Gets the message data.
     *
     * @return array
     *   The message data.
     */
    public function getData(): ?array;

    /**
     * Gets the message purpose.
     *
     * @return string
     *   The message purpose.
     */
    public function getPurpose(): string;

}
