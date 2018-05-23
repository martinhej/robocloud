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
    'roboId' => 'meteorology.garden.air-temperature.south1',
    'purpose' => 'meteorology.measurements.aggregated',
    'data' => [
        'air_humidity' => [
            'value' => rand(10, 100),
            'unit' => 'Percent',
        ],
        'air_temperature' => [
            'value' => rand(10, 30),
            'unit' => 'Celsius',
        ],
        'atm_pressure' => [
            'value' => rand(100000, 102000),
            'unit' => 'Pa',
        ],
        'soil_humidity' => [
            'value' => rand(10, 100),
            'unit' => 'Percent',
        ],
    ],
]);

// Create the actual message that will be sent to Kinesis.
// This will throw exception if message data validation
// fails or if schema files could not be found.
$message = $message_factory->createMessage();

// Create instance of the Kinesis client factory.
$kinesis_factory = new KinesisClientFactory('2013-12-02', 'eu-west-1');

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
$producer->pushAll();

```

#### Consumer example

```php
$consumer = new Consumer();
```
