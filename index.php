<?php

include_once 'vendor/autoload.php';

use robocloud\Config\DefaultConfig;
use robocloud\Message\Message;
use robocloud\Message\MessageFactory;
use robocloud\Message\MessageSchemaValidator;
use Symfony\Component\EventDispatcher\EventDispatcher;

$config = new DefaultConfig();

$event_dispatcher = new EventDispatcher();
$event_dispatcher->addSubscriber(new MessageSchemaValidator($config));

$message_factory = new MessageFactory($event_dispatcher);

$robo_id = 'meteorology.garden.air-temperature.south1';
$date = new \DateTime('now', new \DateTimeZone('UTC'));

$message_factory->setMessageClass(Message::class)->setMessageData([
  'roboId' => $robo_id,
  'purpose' => 'meteorology.measurements.temperature',
  'data' => [
    'value' => rand(10, 30),
    'unit' => 'Celsius',
  ],
]);
$message = $message_factory->createMessage();

var_dump($message);