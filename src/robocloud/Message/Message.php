<?php

namespace robocloud\Message;

/**
 * Class Message.
 *
 * @package robocloud\Message
 */
class Message implements MessageInterface
{

    /**
     * The message version.
     *
     * @var string
     */
    public $version;

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
    public $priority;

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

    /**
     * The message id to which the current message is a response.
     *
     * @var string
     */
    public $responseTo;

    /**
     * List of robo ids to which this message is sent to.
     *
     * @var array
     */
    public $recipients = [];

    /**
     * The recipients wildcard to which this message is sent to.
     *
     * @var string
     */
    public $recipientsWildcard;

    /**
     * Message constructor.
     *
     * @param array $message_data
     *   The message data necessary to initiate a message instance.
     */
    public function __construct(array $message_data)
    {
        foreach ($message_data as $key => $value) {
            if (property_exists(get_called_class(), $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoboId(): string
    {
        return $this->roboId;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageTime(): string
    {
        return $this->messageTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseTo(): ?string
    {
        return $this->responseTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipients(): ?array
    {
        return $this->recipients;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientWildcard(): ?string
    {
        return $this->recipientsWildcard;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        $messageData = [
            'version' => $this->getVersion(),
            'roboId' => $this->getRoboId(),
            'messageId' => $this->getMessageId(),
            'purpose' => $this->getPurpose(),
            'messageTime' => $this->getMessageTime(),
            'data' => $this->getData(),
        ];

        if (empty($messageData['messageTime'])) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));
            $messageData['messageTime'] = $date->format(\DateTime::ISO8601);
        }

        if (empty($messageData['messageId'])) {
            $messageData['messageId'] = sha1(serialize($messageData));
        }

        if ($priority = $this->getPriority()) {
            $messageData['priority'] = $priority;
        }

        if ($tags = $this->getTags()) {
            $messageData['tags'] = $tags;
        }

        if ($response_to = $this->getResponseTo()) {
            $messageData['responseTo'] = $response_to;
        }

        if ($recipients = $this->getRecipients()) {
            $messageData['recipients'] = $recipients;
        }

        if ($wildcard = $this->getRecipientWildcard()) {
            $messageData['recipientWildcard'] = $wildcard;
        }

        return $messageData;
    }

}
