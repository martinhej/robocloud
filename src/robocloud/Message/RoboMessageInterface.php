<?php

namespace robocloud\Message;

interface RoboMessageInterface extends MessageInterface
{
    /**
     * Gets the robot id that emitted the message.
     *
     * Structure: [area].[destination].[purpose].[name]
     *
     * @return string
     *   The robot id.
     */
    public function getRoboId(): string;

    /**
     * Gets the unique identifier of the message.
     *
     * Structure: [roboId].[id].
     *
     * @return string
     *   Namespaced message identification.
     */
    public function getMessageId(): string;

    /**
     * Gets the message priority.
     *
     * @return string
     *   One of the self::PRIORITY_* constants.
     */
    public function getPriority(): ?string;

    /**
     * Gets the response to origin.
     *
     * @return string
     *   The origin of the message that this is response to.
     */
    public function getResponseTo(): ?string;

    /**
     * Gets the list of robo-ids of recipients.
     *
     * @return array
     *   The robo-ids of recipients.
     */
    public function getRecipients(): ?array;

    /**
     * Gets the recipients wildcard.
     *
     * @return string
     *   The recipients wildcard.
     */
    public function getRecipientsWildcard(): ?string;

}
