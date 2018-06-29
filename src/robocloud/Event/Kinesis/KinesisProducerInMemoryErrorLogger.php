<?php

namespace robocloud\Event\Kinesis;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class KinesisProducerInMemoryErrorLogger.
 *
 * @package robocloud\Event\Kinesis
 */
class KinesisProducerInMemoryErrorLogger implements EventSubscriberInterface
{

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param KinesisProducerError $error
     */
    public function processError(KinesisProducerError $error)
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
            KinesisProducerError::NAME => 'processError',
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
