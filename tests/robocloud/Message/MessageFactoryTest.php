<?php

namespace robocloud\Tests\Message;

use PHPUnit\Framework\TestCase;
use robocloud\Message\RoboMessage;
use robocloud\Message\MessageFactory;
use robocloud\Message\MessageSchemaValidator;
use robocloud\Message\SchemaDiscovery;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MessageFactoryTest extends TestCase
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    public function setUp()
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->messageFactory = new MessageFactory(RoboMessage::class, $this->eventDispatcher);
    }

    public function testSimpleMessageCreation()
    {
        $message = $this->messageFactory->setMessageData([
            'version' => 'v_0_1',
            'roboId' => 'robo.test',
            'purpose' => 'buddy.find',
            'data' => ['reason' => 'Lost in space'],
        ])->createMessage();

        $this->assertEquals('v_0_1', $message->getVersion());
        $this->assertEquals('robo.test', $message->getRoboId());
        $this->assertEquals('buddy.find', $message->getPurpose());
        $this->assertEquals('Lost in space', $message->getData()['reason']);
    }

    /**
     * @expectedException \robocloud\Exception\InvalidMessageDataException
     * @expectedExceptionMessage Could not find message with purpose "__buddy.find"
     */
    public function testInvalidDataMessageCreation() {
        $container = new Container(new ParameterBag([
            'robocloud' => ['message_schema_dirs' => [
                __DIR__ . '/../../../schema/message'
            ]],
        ]));

        $cache = new ArrayCache();

        $schema_validator = new MessageSchemaValidator(new SchemaDiscovery($container, $cache));
        $this->eventDispatcher->addSubscriber($schema_validator);

        $this->messageFactory->setMessageData([
            'messageId' => '123',
            'version' => 'v_0_1',
            'messageTime' => '',
            'roboId' => 'test',
            'purpose' => '__buddy.find',
            'data' => ['reason' => 'lost in space'],
        ])->createMessage();
    }
}
