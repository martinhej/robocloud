<?php

namespace robocloud\Tests\Message;

use PHPUnit\Framework\TestCase;
use robocloud\Event\MessageComposedEvent;
use robocloud\Message\RoboMessage;
use robocloud\Message\MessageSchemaValidator;
use robocloud\Message\SchemaDiscovery;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageSchemaValidatorTest.
 *
 * @package robomaze\robocloud\Test\Message
 */
class MessageSchemaValidatorTest extends TestCase
{
    /**
     * @var SchemaDiscovery
     */
    protected $schemaDiscovery;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
        $container = new Container(new ParameterBag([
            'robocloud' => ['message_schema_dirs' => [
                __DIR__ . '/../../../schema/message'
            ]],
        ]));

        $cache = new ArrayCache();

        $this->schemaDiscovery = new SchemaDiscovery($container, $cache);
    }

    /**
     * Tests valid schema validation.
     *
     */
    public function testCorrectSchemaValidation()
    {
        $schema_validator = new MessageSchemaValidator($this->schemaDiscovery);

        $this->assertTrue(in_array(EventSubscriberInterface::class, class_implements($schema_validator)));

        $message = new RoboMessage([
          'messageId' => '123',
          'version' => 'v_0_1',
          'messageTime' => '',
          'roboId' => 'test',
          'purpose' => 'buddy.find',
          'data' => ['reason' => 'lost in space'],
        ]);
        $event = new MessageComposedEvent($message);

        $schema_validator->validate($event);

    }

    /**
     * @expectedException \robocloud\Exception\InvalidMessageDataException
     * @expectedExceptionMessage The message with purpose buddy.find is missing required data: reason
     */
    public function testMissingRequiredData()
    {
        $schema_validator = new MessageSchemaValidator($this->schemaDiscovery);

        $message = new RoboMessage([
          'messageId' => '123',
          'version' => 'v_0_1',
          'messageTime' => '',
          'roboId' => 'test',
          'purpose' => 'buddy.find',
          'data' => [],
        ]);
        $event = new MessageComposedEvent($message);
        $schema_validator->validate($event);
    }

    /**
     * @expectedException \robocloud\Exception\InvalidMessageDataException
     * @expectedExceptionMessage Could not find message with purpose "___buddy.find"
     */
    public function testMalformedPurpose()
    {
        $schema_validator = new MessageSchemaValidator($this->schemaDiscovery);

        $message = new RoboMessage([
          'messageId' => '123',
          'version' => 'v_0_1',
          'messageTime' => '',
          'roboId' => 'test',
          'purpose' => '___buddy.find',
          'data' => [],
        ]);
        $event = new MessageComposedEvent($message);
        $schema_validator->validate($event);
    }

}
