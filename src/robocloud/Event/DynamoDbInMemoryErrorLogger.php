<?php

namespace robocloud\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DynamoDbInMemoryErrorLogger.
 *
 * @package robocloud\Event
 */
class DynamoDbInMemoryErrorLogger implements EventSubscriberInterface
{

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param DynamoDbError $error
     */
    public function processError(DynamoDbError $error)
    {
        $this->errors[] = [
            'message' => $error->getException()->getMessage(),
            'exception' => $error->getException(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DynamoDbError::NAME => 'processError',
        ];
    }

    /**
     * Gets logged errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
