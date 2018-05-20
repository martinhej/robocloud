<?php

namespace robocloud\Kinesis\Client;

/**
 * Interface ConsumerRecoveryInterface.
 *
 * @package robocloud\Kinesis\Client
 */
interface ConsumerRecoveryInterface
{

    /**
     * Stores last success position of the Kinesis Consumer.
     *
     * @param string $shard_id
     *   The shard id from which the Consumer was reading.
     * @param string $sequence_number
     *   The sequence number of the last successfully processed event.
     */
    public function storeLastSuccessPosition($shard_id, $sequence_number);

    /**
     * Checks if there are recovery data.
     *
     * @return bool
     *   TRUE if there are recovery data, FALSE otherwise.
     */
    public function hasRecoveryData();

    /**
     * Gets the last sequence number.
     *
     * @param string $shard_id
     *   The shard id for which to get the last sequence number.
     *
     * @return string
     *   The sequence number.
     */
    public function getLastSequenceNumber($shard_id);

}
