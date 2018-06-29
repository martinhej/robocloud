<?php

namespace robocloud\Event\DynamoDb;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DynamoDbErrorConsoleLogger.
 *
 * @package robocloud\Event\DynamoDb
 */
class DynamoDbErrorConsoleLogger implements EventSubscriberInterface
{

    /**
     * @param DynamoDbError $error
     */
    public function processError(DynamoDbError $error)
    {
        var_dump('=== DynamoDB ERROR ===', $error->getException()->getMessage(), '=== ERROR END ===');
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

}
