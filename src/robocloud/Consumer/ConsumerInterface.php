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
     * Consumes messages from a stream.
     *
     * @param int $run_time
     * @param string $shard_id
     */
    public function consume(int $run_time = 0, string $shard_id);

    /**
     * Gets number of milliseconds of the last record being behind latest.
     *
     * @return int
     *   Milliseconds behind latest.
     */
    public function getLag(): int;

}
