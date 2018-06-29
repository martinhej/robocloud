<?php

namespace robocloud\Consumer\MessageProcessing\Backend;

/**
 * Processes any consumed data from a stream.
 */
interface BackendInterface
{

    /**
     * Adds processed message data for being stored.
     *
     * @param mixed $data
     *   Message data returned by the TransformerInterface implementation.
     */
    public function add($data);

    /**
     * Takes any required action upon receiving robocloud data.
     */
    public function process();

}
