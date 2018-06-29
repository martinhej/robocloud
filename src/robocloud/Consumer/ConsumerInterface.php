<?php

namespace robocloud\Consumer;

/**
 * Interface ConsumerInterface.
 *
 * @package robocloud\Kinesis
 */
interface ConsumerInterface
{

    /**
     * Sets consumer data needed for proper function.
     *
     * @param array $data
     *   The consumer data.
     *
     * @return ConsumerInterface
     */
    public function setConsumerData(array $data): ConsumerInterface;

    /**
     * Consumes messages from a stream.
     *
     * @param int $runTime
     */
    public function consume($runTime = 0);

    /**
     * Gets number of milliseconds of the last record being behind latest.
     *
     * @return int
     *   Milliseconds behind latest.
     */
    public function getLag();

}
