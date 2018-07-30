<?php

namespace robocloud\Message;

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
    protected $schemaDiscovery;

    /**
     * MessageSchemaValidator constructor.
     *
     * @param SchemaDiscovery $discovery
     */
    public function __construct(SchemaDiscovery $discovery)
    {
        $this->schemaDiscovery = $discovery;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(MessageComposedEvent $event)
    {

        $message = $event->getMessage();

        $schema = $this->schemaDiscovery->getGeneralMessageSchema();
        $message_data = $message->jsonSerialize();

        foreach ($schema->required as $required_property) {
            if (empty($message_data[$required_property])) {
                throw new InvalidMessageDataException('The message with purpose ' . $message->getPurpose() . ' is missing required property: ' . $required_property);
            }
        }

        $message_data_schema = $this->schemaDiscovery->getMessageDataSchema($message);

        foreach ($message_data_schema->required as $required_property) {
            if (empty($message_data['data'][$required_property])) {
                throw new InvalidMessageDataException('The message with purpose ' . $message->getPurpose() . ' is missing required data: ' . $required_property);
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

}
