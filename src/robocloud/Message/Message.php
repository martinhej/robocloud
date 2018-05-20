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
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoboId()
    {
        return $this->roboId;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageTime()
    {
        return $this->messageTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseTo()
    {
        return $this->responseTo;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientWildcard()
    {
        return $this->recipientsWildcard;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $messageData = [
            'version' => $this->getVersion(),
            'roboId' => $this->getRoboId(),
            'messageId' => $this->getMessageId(),
            'priority' => $this->getPriority(),
            'purpose' => $this->getPurpose(),
            'tags' => $this->getTags(),
            'messageTime' => $this->getMessageTime(),
            'data' => $this->getData(),
            'responseTo' => $this->getResponseTo(),
            'recipients' => $this->getRecipients(),
            'recipientWildcard' => $this->getRecipientWildcard(),
        ];

        if (empty($messageData['messageTime'])) {
            $date = new \DateTime('now', new \DateTimeZone('UTC'));
            $messageData['messageTime'] = $date->format(\DateTime::ISO8601);
        }

        if (empty($messageData['messageId'])) {
            $messageData['messageId'] = sha1(serialize($messageData));
        }

        return $messageData;
    }

}
