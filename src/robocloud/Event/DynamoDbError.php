<?php

namespace robocloud\Event;

use robocloud\Message\MessageInterface;
use Symfony\Component\EventDispatcher\Event;

class DynamoDbError extends Event
{

    const NAME = 'robocloud.dynamodb.error';

    /**
     * @var \Exception
     */
    protected $exception;

    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

}
