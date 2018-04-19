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
class MessageSchemaValidator implements MessageValidatorInterface, EventSubscriberInterface {

  /**
   * The message to be validated.
   *
   * @var \robocloud\Message\MessageInterface
   */
  protected $message;

  /**
   * @var ConfigInterface
   */
  protected $config;

  /**
   * MessageSchemaValidator constructor.
   *
   * @param ConfigInterface $config
   */
  public function __construct(ConfigInterface $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(MessageComposedEvent $event) {

    $message = $event->getMessage();

    $schema = $this->getGeneralMessageSchema();
    $message_data = $message->jsonSerialize();

    foreach ($schema->required as $required_property) {
      if (empty($message_data[$required_property])) {
        throw new InvalidMessageDataException('The message from ' . $message->getRoboId() . ' is missing required property: ' . $required_property);
      }
    }

    $message_data_schema = $this->getMessageDataSchema($message);

    foreach ($message_data_schema->required as $required_property) {
      if (empty($message_data['data'][$required_property])) {
        throw new InvalidMessageDataException('The message from ' . $message->getRoboId() . ' is missing required data: ' . $required_property);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
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
  protected function getGeneralMessageSchema() {

    $path = $this->config->getMessageSchemaDir() . '/message.' . $this->config->getMessageSchemaVersion() . '.schema.json';

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
   * @param \robocloud\Message\MessageInterface $message
   *   The Message object.
   *
   * @return object
   *   The message data property schema.
   */
  protected function getMessageDataSchema(MessageInterface $message) {
    $parts = explode('.', $message->getPurpose());

    if (count($parts) != 3) {
      throw new \InvalidArgumentException('The message "purpose" property should consist of three parts delimited by the dot (.) character');
    }

    $file_name = $parts[2] . '.schema.json';

    $path =
      $this->config->getMessageSchemaDir() . '/' . $this->config->getMessageSchemaVersion() . '/' .
      $parts[0] . '/' .
      $parts[1] . '/' .
      $file_name;

    if (file_exists($path)) {
      $schema = json_decode(file_get_contents($path));
    }

    if (empty($schema)) {
      throw new \InvalidArgumentException('Message schema not found: ' . $path);
    }

    return $schema;
  }

}
