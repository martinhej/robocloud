<?php

namespace robocloud\Kinesis;

/**
 * Interface ClientInterface.
 *
 * @package robocloud\Kinesis
 */
interface KinesisServiceInterface
{

    /**
     * Gets the stream name.
     *
     * @return string
     *   The stream name.
     */
    public function getStreamName();

    /**
     * Gets the Kinesis client.
     *
     * @return \Aws\Kinesis\KinesisClient
     *   The Kinesis client.
     */
    public function getClient();

    /**
     * Processes an error.
     *
     * @param \Exception $exception
     *   The exception.
     * @param array $data
     *   Data relevant to the error that occurred.
     */
    public function processError(\Exception $exception, array $data = []);

    /**
     * Gets the message factory.
     *
     * @return \robocloud\Message\MessageFactoryInterface
     *   The message factory.
     */
    public function getMessageFactory();

}
