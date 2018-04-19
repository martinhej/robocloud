<?php

namespace robocloud\Tests\Message;

use robocloud\Message\Message;
use robocloud\Message\MessageFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class MessageSchemaValidatorTest.
 *
 * @package robomaze\robocloud\Test\Message
 */
class MessageSchemaValidatorTest extends KernelTestCase {

  /**
   * @var MessageFactoryInterface
   */
  private $messageFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    $kernel = static::createKernel();
    $kernel->boot();

    self::$container = $kernel->getContainer();

    $this->messageFactory = self::$container->get('robocloud.message.factory');
  }

  public function testCorrectSchemaValidation() {
    $this->messageFactory->setMessageClass(Message::class);
    $this->messageFactory->setMessageData([]);

    $message = $this->messageFactory->createMessage();

    var_dump($message);

    $this->assertTrue(true);
  }

}
