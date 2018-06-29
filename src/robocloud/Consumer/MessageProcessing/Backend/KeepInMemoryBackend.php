<?php

namespace robocloud\Consumer\MessageProcessing\Backend;

/**
 * Keeps consumed messages in memory buffer for further processing.
 */
class KeepInMemoryBackend implements BackendInterface
{

    /**
     * Collected messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function add($data)
    {
        $this->messages[] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {

    }

    /**
     * Gets Messages and flushes the queue.
     *
     * @return array
     *   The Messages.
     */
    public function flush() : array
    {
        $messages = $this->messages;
        $this->messages = [];

        return $messages;
    }

}
