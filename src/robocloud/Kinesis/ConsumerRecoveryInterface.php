<?php

namespace robocloud\Kinesis;

/**
 * Consumer recovery to store and retrieve last read position.
 */
interface ConsumerRecoveryInterface
{

    /**
     * Should set any consumer specific data.
     *
     * @param array $data
     *
     * @return ConsumerRecoveryInterface
     */
    public function setConsumerData(array $data) : ConsumerRecoveryInterface;

    /**
     * Stores last success position of the Kinesis Consumer.
     *
     * @param string $sequence_number
     *   The sequence number of the last successfully processed event.
     */
    public function storeLastSuccessPosition($sequence_number);

    /**
     * Checks if there are recovery data.
     *
     * @return bool
     *   TRUE if there are recovery data, FALSE otherwise.
     */
    public function hasRecoveryData() : bool;

    /**
     * Gets the last sequence number.
     *
     * @return string
     *   The sequence number.
     */
    public function getLastSequenceNumber() : string;

    /**
     * Gets shard id from which to read.
     *
     * @return string
     */
    public function getShardId() : string;

    /**
     * Gets stream name.
     *
     * @return string
     */
    public function getStreamName() : string;

}
