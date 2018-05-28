# robocloud - the space where robots chatter

## warning
Note that this code is highly experimental and is expected to change
in near future breaking the backwards compatibility.

As well as the concepts of robocloud and robotalk are experimental
being far from complete.

Despite of that it is already usable for simple things like maze 
solving robots cooperation or targeting single or more robots with
a specific set of instructions to be executed. So do not hesitate 
to use it for your experimental robotic fun projects!!! :)

## The high overview
The idea is to provide an infrastructure where two or more systems may
communicate in all directions so that they can not only exchange
data but also problem solving instruction sets to also enable mutual 
learning.

### Robotalk
Robotalk would be the "language" used to perform such communication.

#### Messages and schemas
Each message is defined by two schema files. The first one being the 
general message schema that defines basic message structure that is
common for all messages. The second one defines the "data" property
structure that varies based on the message "purpose".

Currently the message schemas library is part of this project under
the "schema" directory. Note that the message "purpose" property 
defines the directory structure where the actual schema file resides.


### Robocloud
Robocloud would be the platform that technically enables the use of
Robotalk.

#### Provided functionality
Robocloud provides functionality to push and read specific messages
from a AWS Kinesis stream. It follows the concept of Kinesis message 
Producer and Consumer. It does most of the heavy lifting to utilize 
Kinesis streams adding the possibility to validate messages and 
process messages when being consumed from a stream.

#### Producer example

```php

use robocloud\Event\KinesisProducerErrorConsoleLogger;
use robocloud\Kinesis\Client\Producer;
use robocloud\KinesisClientFactory;
use robocloud\Message\Message;
use robocloud\Message\MessageFactory;
use robocloud\Message\MessageSchemaValidator;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\EventDispatcher\EventDispatcher;

// Define the Kinesis stream name.
$stream_name = 'robocloud';

// Get an event dispatcher instance.
$event_dispatcher = new EventDispatcher();

// Add message schema validator.
$event_dispatcher->addSubscriber(new MessageSchemaValidator('schema/stream/robocloud/message'));

// Add error processors. A few simple ones that passivly log errors
// are available in robocloud/Event. To provide more robust error
// processing like requeuing failed messages you need to provide
// your own.
$event_dispatcher->addSubscriber(new KinesisProducerErrorConsoleLogger());

// Now get the message facory that will create and validate messages
// for you.
$message_factory = new MessageFactory(Message::class, $event_dispatcher);

// Use the message factory to set the message data.
$message_factory->setMessageData([
    'version' => 'v_0_1',
    'roboId' => 'lost',
    'purpose' => 'buddy.find',
    'data' => [
        'reason' => 'line_follower.line.lost',
    ],
]);

// Create the actual message that will be sent to Kinesis.
// This will throw exception if message data validation
// fails or if schema files could not be found.
$message = $message_factory->createMessage();

// Create instance of the Kinesis client factory.
$kinesis_factory = new KinesisClientFactory('2013-12-02', 'eu-west-1');

// Provide cache that will be used to cache shard info.
$cache = new FilesystemCache();

// Create the Producer instance.
$producer = new Producer(
    $kinesis_factory->getKinesisClient('AKIAJG2QTSBDKBFNACDA', 'Pg2c2AzMfY/5koj6b0IO3GgOvgF/m5nUDayjBOh/'),
    $stream_name,
    $message_factory,
    $event_dispatcher,
    $cache
);

// Add the message and push it to the stream.
$producer->add($message);
var_dump(array_map(function($result) {
    return (string) $result;
}, $producer->pushAll()));

```

#### Consumer example

```php

// Define the Kinesis stream name.
use robocloud\Event\KinesisConsumerErrorConsoleLogger;
use robocloud\Kinesis\Client\Consumer;
use robocloud\Kinesis\Client\ConsumerRecovery;
use robocloud\KinesisClientFactory;
use robocloud\Message\Message;
use robocloud\Message\MessageFactory;
use robocloud\Message\MessageSchemaValidator;
use robocloud\MessageProcessing\Backend\KeepInMemoryBackend;
use robocloud\MessageProcessing\Filter\KeepAllFilter;
use robocloud\MessageProcessing\Processor\DefaultProcessor;
use robocloud\MessageProcessing\Transformer\KeepOriginalTransformer;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\EventDispatcher\EventDispatcher;

// Define the Kinesis stream name.
$stream_name = 'robocloud';

// Create event dispatcher instance.
$event_dispatcher = new EventDispatcher();

// Add message schema validator.
$event_dispatcher->addSubscriber(new MessageSchemaValidator('schema/stream/robocloud/message'));

// Add error handler(s).
$event_dispatcher->addSubscriber(new KinesisConsumerErrorConsoleLogger());

// Create filter instance that will be used to filter out only those messages
// that you are interested in.
$filter = new KeepAllFilter();
// The transformer layer is responsible for extracting and processing the
// message data into a form that is expected by your backend.
$keep_original_transformer = new KeepOriginalTransformer();
// Finally provide your backend that will finish the message processing.
$keep_in_memory_backend = new KeepInMemoryBackend();

// Add the message processor as the subscriber that will be used
// during consuming to process the messages.
$event_dispatcher->addSubscriber(new DefaultProcessor($filter, $keep_original_transformer, $keep_in_memory_backend));

// Get the message factory that will be used for creating the message objects
// from the data pulled from Kinesis.
$message_factory = new MessageFactory(Message::class, $event_dispatcher);

// Create instance of the Kinesis client factory.
$kinesis_factory = new KinesisClientFactory('2013-12-02', 'eu-west-1');

// Provide cache that will be used to cache shard info.
$cache = new FilesystemCache();

// Provide the recovery object used to store last read position.
$consumer_recovery = new ConsumerRecovery($stream_name, '/tmp/consumer_recovery.rec');

// Instantiate the consumer and consume messages from Kinesis stream.
$consumer = new Consumer(
    $kinesis_factory->getKinesisClient('AKIAINK5P33X2KBK2RAQ', 'EuUdvE7WW0SKaEpGWMWHvN5M+gIjGaoLAVTYzzhV'),
    $stream_name,
    $message_factory,
    $event_dispatcher,
    $cache,
    $consumer_recovery
);

// One process
$consumer->consume(0);

// Print the messages to see what we pulled from the stream.
var_dump($keep_in_memory_backend->flush());

// Note that this example is very trivial not providing any real functionality.
// To get better idea on how to use message processor see other filter,
// transformer and backend classes in the MessageProcessing namespace.

```
