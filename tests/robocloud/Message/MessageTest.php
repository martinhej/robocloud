<?php

namespace robocloud\Tests\Message;

use PHPUnit\Framework\TestCase;
use robocloud\Message\RoboMessage;

class MessageTest extends TestCase
{
    /**
     * Tests if message is instantiated with all its properties.
     */
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

        $message = new RoboMessage($message_data);

        $this->assertNotEmpty($message->getMessageId(), 'Message Id was generated');

        foreach ($message_data as $key => $value) {
            $this->assertEquals($value, $message->{"get" . ucfirst($key)}());
        }
    }
}
