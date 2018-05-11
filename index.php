<?php

include_once 'vendor/autoload.php';

use robocloud\Config\DefaultConfig;
use robocloud\DynamoDbClientFactory;
use robocloud\Event\DynamoDbErrorConsoleLogger;
use robocloud\Event\KinesisConsumerErrorConsoleLogger;
use robocloud\Event\KinesisProducerErrorConsoleLogger;
use robocloud\Kinesis\Client\Consumer;
use robocloud\Kinesis\Client\ConsumerRecovery;
use robocloud\Kinesis\Client\Producer;
use robocloud\KinesisClientFactory;
use robocloud\Message\Message;
use robocloud\Message\MessageFactory;
use robocloud\Message\MessageSchemaValidator;
use robocloud\MessageProcessing\Backend\DynamoDbBackend;
use robocloud\MessageProcessing\Backend\KeepInMemoryBackend;
use robocloud\MessageProcessing\Filter\KeepAllFilter;
use robocloud\MessageProcessing\Processor\DefaultProcessor;
use robocloud\MessageProcessing\Transformer\DynamoDbTransformer;
use robocloud\MessageProcessing\Transformer\KeepOriginalTransformer;
use Symfony\Component\EventDispatcher\EventDispatcher;

// Create instance of the robocloud config.
$config = new DefaultConfig('config/robocloud.yml');

// Create event dispatcher instance.
$event_dispatcher = new EventDispatcher();

// Add message schema validator.
$event_dispatcher->addSubscriber(new MessageSchemaValidator($config));

// Add error loggers.
$event_dispatcher->addSubscriber(new KinesisConsumerErrorConsoleLogger());
$event_dispatcher->addSubscriber(new KinesisProducerErrorConsoleLogger());
$event_dispatcher->addSubscriber(new DynamoDbErrorConsoleLogger());

/*
 * Message producing example.
 */

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

// Instantiate the producer...
$kinesis_factory = new KinesisClientFactory($config);

$producer = new Producer(
  $kinesis_factory->getKinesisClient('producer'),
  $config->getStreamName(),
  $message_factory,
  $event_dispatcher
);

// ... add the message and push it to the Kinesis stream.
$producer->add($message);
$producer->pushAll();

/*
 * Message consuming and processing example.
 */

// Create instances needed for message processing: the filter, transformer
// and backend.
$filter = new KeepAllFilter();
$dynamodb_transformer = new DynamoDbTransformer();
$dynamodb_factory = new DynamoDbClientFactory($config);
$dynamodb_backend = new DynamoDbBackend($dynamodb_factory->getDynamoDbClient(), $config->getStreamName(), $event_dispatcher);

$keep_original_transformer = new KeepOriginalTransformer();
$keep_in_memory_backend = new KeepInMemoryBackend();

// Add the subscriber for message processing.
$event_dispatcher->addSubscriber(new DefaultProcessor($filter, $dynamodb_transformer, $dynamodb_backend));
$event_dispatcher->addSubscriber(new DefaultProcessor($filter, $keep_original_transformer, $keep_in_memory_backend));

// Instantiate the consumer and consume messages from Kinesis stream.
$consumer = new Consumer(
  $kinesis_factory->getKinesisClient('consumer'),
  $config->getStreamName(),
  $message_factory,
  $event_dispatcher,
  new ConsumerRecovery($config)
);

$consumer->consume(0);

var_dump($keep_in_memory_backend->flush());
