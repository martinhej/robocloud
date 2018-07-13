<?php

namespace robocloud\Tests\Message;

use PHPUnit\Framework\TestCase;
use robocloud\Message\Message;

class MessageTest extends TestCase
{
    public function setUp()
    {

    }

    public function testMessageInstantiation()
    {
        $message_data = [
            'version' => 'v_0_1',
            'messageTime' => date('Y-m-d H:i:s'),
            'roboId' => 'test',
            'purpose' => 'buddy.find',
            'data' => ['reason' => 'lost in space'],
            'priority' => '3',
            'tags' => ['tag1', 'tag2'],
            'responseTo' => 'response-to-message-id',
            'recipients' => ['test.robo.1', 'test.robo.2'],
            'recipientsWildcard' => 'test.robo.*',
        ];

        $message = new Message($message_data);

        $this->assertNotEmpty($message->getMessageId(), 'Message Id was generated');

        foreach ($message_data as $key => $value) {
            $this->assertEquals($value, $message->{"get" . ucfirst($key)}());
        }
    }
}
