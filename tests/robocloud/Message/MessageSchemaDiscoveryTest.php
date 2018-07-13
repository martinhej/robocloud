<?php

namespace robocloud\Tests\Message;

use PHPUnit\Framework\TestCase;
use robocloud\Message\Message;
use robocloud\Message\SchemaDiscovery;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Tests the schema discovery.
 */
class MessageSchemaDiscoveryTest extends TestCase
{

    /**
     * Tests discovery of general message schema.
     */
    public function testGeneralSchemaDiscovery() {
        $container = new Container(new ParameterBag([
            'robocloud' => ['message_schema_dirs' => []],
        ]));

        $cache = new ArrayCache();

        $schema_discovery = new SchemaDiscovery($container, $cache);

        $general_schema = $schema_discovery->getGeneralMessageSchema();

        $this->assertEquals('robomessage', $general_schema->title);
    }

    /**
     * Tests that message data schemas get loaded properly.
     */
    public function testMessageSchemaDiscovery() {
        $container = new Container(new ParameterBag([
            'robocloud' => ['message_schema_dirs' => [
                __DIR__ . '/../../../schema/message'
            ]],
        ]));

        $cache = new ArrayCache();

        $schema_discovery = new SchemaDiscovery($container, $cache);

        $message = new Message([
            'version' => 'v_0_1',
            'roboId' => 'robo.test',
            'purpose' => 'buddy.find',
            'data' => ['reason' => 'Lost in space'],
        ]);

        $schema = $schema_discovery->getMessageDataSchema($message);

        $this->assertEquals('Find buddy', $schema->title);

        $message = new Message([
            'version' => 'v_0_1',
            'roboId' => 'robo.help.test',
            'purpose' => 'buddy.respond',
            'data' => ['introduction' => 'I am your new buddy'],
        ]);

        $schema = $schema_discovery->getMessageDataSchema($message);

        $this->assertEquals('Your new buddy', $schema->title);
    }

    /**
     * @expectedException \robocloud\Exception\InvalidMessageDataException
     * @expectedExceptionMessage Could not find message with purpose "buddy.find"
     */
    public function testMessageSchemaDiscoveryBadVersion() {
        $container = new Container(new ParameterBag([
            'robocloud' => ['message_schema_dirs' => [
                __DIR__ . '/../../../schema/message'
            ]],
        ]));

        $cache = new ArrayCache();

        $schema_discovery = new SchemaDiscovery($container, $cache);

        $message = new Message([
            'version' => 'bad-version',
            'roboId' => 'robo.test',
            'purpose' => 'buddy.find',
            'data' => ['reason' => 'Lost in space'],
        ]);

        $schema_discovery->getMessageDataSchema($message);
    }
}
