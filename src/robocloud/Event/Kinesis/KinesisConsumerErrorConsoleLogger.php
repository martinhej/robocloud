<?php

namespace robocloud\Event\Kinesis;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class KinesisConsumerErrorConsoleLogger.
 *
 * @package robocloud\Event\Kinesis
 */
class KinesisConsumerErrorConsoleLogger implements EventSubscriberInterface
{

    /**
     * @param KinesisConsumerError $error
     */
    public function processError(KinesisConsumerError $error)
    {
        var_dump('=== Kinesis Consumer ERROR ===', $error->getException()->getMessage(), '=== ===');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KinesisConsumerError::NAME => 'processError',
        ];
    }

}
