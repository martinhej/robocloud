<?php

namespace robocloud\Kinesis;

/**
 * Consumer recovery to store and retrieve last read position.
 */
interface ConsumerRecoveryInterface
{

    /**
     * Stores last success position of the Kinesis Consumer.
     *
     * @param string $position
     *   The sequence number of the last successfully processed event.
     */
    public function storeLastSuccessPosition($position);

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
    public function getLastSuccessPosition(): string;

}
