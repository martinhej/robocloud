<?php

namespace robocloud\Tests\Message;

use PHPUnit\Framework\TestCase;
use robocloud\Config\ConfigInterface;
use robocloud\Config\DefaultConfig;
use robocloud\Message\Message;
use robocloud\Message\MessageFactory;
use robocloud\Message\MessageFactoryInterface;
use robocloud\Message\MessageSchemaValidator;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class MessageSchemaValidatorTest.
 *
 * @package robomaze\robocloud\Test\Message
 */
class MessageSchemaValidatorTest extends TestCase
{

    /**
     * @var MessageFactoryInterface
     */
    protected $messageFactory;

    protected $eventDispatcher;

    protected $config;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->messageFactory = new MessageFactory(Message::class, $this->eventDispatcher);
    }

    public function testCorrectSchemaValidation()
    {
        $schema_validator = new MessageSchemaValidator(__DIR__ . '/../../../schema/stream/robocloud/message');

        $schema = $schema_validator->getGeneralMessageSchema();

        $this->assertEquals($schema->title, 'robomessage');
    }

}
