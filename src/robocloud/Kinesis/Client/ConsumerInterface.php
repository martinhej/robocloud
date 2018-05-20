<?php

namespace robocloud\Kinesis\Client;

/**
 * Interface ConsumerInterface.
 *
 * @package robocloud\Kinesis\Client
 */
interface ConsumerInterface
{

    /**
     * Do a single call to a Kinesis stream to get messages.
     *
     * @param string $shardId
     *   The shard id from which to start reading.
     * @param string $lastSequenceNumber
     *   The last record sequence number the last call of getRecords() ended. The
     *   value will be provided by a subsequent call of getLastSequenceNumber().
     *
     * @return \robocloud\Message\MessageInterface[]
     *   The messages.
     */
    public function getMessages($shardId = NULL, $lastSequenceNumber = NULL);

    /**
     * Consumes messages from Kinesis stream.
     *
     * @param int $shardPosition
     * @param int $runTime
     * @return mixed
     */
    public function consume($shardPosition, $runTime = 0);

    /**
     * Gets number of milliseconds of the last record being behind latest.
     *
     * @return int
     *   Milliseconds behind latest.
     */
    public function getLag();

    /**
     * Gets the last sequence number.
     *
     * @return string
     *   Sequence number.
     */
    public function getLastSequenceNumber();

    /**
     * Gets the last shard id.
     *
     * @return string
     *   The shard id.
     */
    public function getLastShardId();

    /**
     * Set the batch size.
     *
     * With setting the batch size the overall number of records returned by
     * getRecords() may be approximately controlled as the maximum number of
     * records is equal to batch size value.
     *
     * @param int $size
     *   The number of records pulled in one call.
     *
     * @return $this
     */
    public function setBatchSize($size);

    /**
     * Sets the initial shard iterator type.
     *
     * @param string $type
     *   The shard iterator type. May be 'TRIM_HORIZON' or 'LATEST'.
     *
     * @return $this
     *
     * @see http://docs.aws.amazon.com/kinesis/latest/APIReference/API_GetShardIterator.html
     */
    public function setInitialShardIteratorType($type);

}
