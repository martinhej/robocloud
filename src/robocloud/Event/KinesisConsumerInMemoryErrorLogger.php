<?php

namespace robocloud\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class KinesisConsumerInMemoryErrorLogger.
 *
 * @package robocloud\Event
 */
class KinesisConsumerInMemoryErrorLogger implements EventSubscriberInterface
{

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param KinesisConsumerError $error
     */
    public function processError(KinesisConsumerError $error)
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
            KinesisConsumerError::NAME => 'processError',
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
