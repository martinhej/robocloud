<?php

namespace robocloud\Message;

use robocloud\Config\ConfigInterface;
use robocloud\Event\MessageComposedEvent;
use robocloud\Exception\InvalidMessageDataException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageSchemaValidator.
 *
 * Provides simple validation for the required properties defined by the schema.
 *
 * @package robocloud\Message
 */
class MessageSchemaValidator implements MessageValidatorInterface, EventSubscriberInterface
{

    /**
     * The message to be validated.
     *
     * @var \robocloud\Message\MessageInterface
     */
    protected $message;

    /**
     * @var string
     */
    protected $messageSchemaDir;

    /**
     * MessageSchemaValidator constructor.
     *
     * @param string $messge_schema_dir
     */
    public function __construct($messge_schema_dir)
    {
        $this->messageSchemaDir = $messge_schema_dir;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(MessageComposedEvent $event)
    {

        $this->message = $event->getMessage();

        $schema = $this->getGeneralMessageSchema();
        $message_data = $this->getMessage()->jsonSerialize();

        foreach ($schema->required as $required_property) {
            if (empty($message_data[$required_property])) {
                throw new InvalidMessageDataException('The message from ' . $this->getMessage()->getRoboId() . ' is missing required property: ' . $required_property);
            }
        }

        $message_data_schema = $this->getMessageDataSchema();

        foreach ($message_data_schema->required as $required_property) {
            if (empty($message_data['data'][$required_property])) {
                throw new InvalidMessageDataException('The message from ' . $this->getMessage()->getRoboId() . ' is missing required data: ' . $required_property);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            MessageComposedEvent::NAME => 'validate',
        ];
    }

    /**
     * Gets the general message schema.
     *
     * @return object
     *   The general message schema.
     */
    public function getGeneralMessageSchema()
    {

        $path = $this->getMessageSchemaDir() . '/message.' . $this->getMessage()->getVersion() . '.schema.json';

        if (file_exists($path)) {
            $schema = json_decode(file_get_contents($path));
        }

        if (empty($schema)) {
            throw new \InvalidArgumentException('General message schema not found at ' . $path);
        }

        return $schema;
    }

    /**
     * Gets message schema.
     *
     * @return object
     *   The message data property schema.
     *
     * @throws InvalidMessageDataException
     */
    public function getMessageDataSchema()
    {
        $parts = explode('.', $this->getMessage()->getPurpose());

        if (count($parts) != 3) {
            throw new InvalidMessageDataException('The message "purpose" property should consist of three parts delimited by the dot (.) character');
        }

        $file_name = $parts[2] . '.schema.json';

        $path =
            $this->getMessageSchemaDir() . '/' . $this->getMessage()->getVersion() . '/' .
            $parts[0] . '/' .
            $parts[1] . '/' .
            $file_name;

        if (file_exists($path)) {
            $schema = json_decode(file_get_contents($path));
        }

        if (empty($schema)) {
            throw new InvalidMessageDataException('Message schema not found: ' . $path);
        }

        return $schema;
    }

    /**
     * Gets the message schema directory.
     *
     * @return string
     *   The message schema directory.
     */
    public function getMessageSchemaDir()
    {
        return $this->messageSchemaDir;
    }

    /**
     * Gets the message.
     *
     * @return MessageInterface
     *   The message to be validated.
     */
    public function getMessage() {
        return $this->message;
    }

}
