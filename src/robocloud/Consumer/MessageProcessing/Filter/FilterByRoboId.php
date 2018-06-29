<?php

namespace robocloud\Consumer\MessageProcessing\Filter;

use robocloud\Message\MessageInterface;

/**
 * Filters consumed messages by robo id.
 */
class FilterByRoboId implements FilterInterface
{

    /**
     * The Message purpose.
     *
     * @var string
     */
    protected $roboId;

    /**
     * FilterByPurpose constructor.
     *
     * @param string $roboId
     *   The Message purpose.
     */
    public function __construct($roboId)
    {
        $this->roboId = $roboId;
    }

    /**
     * {@inheritdoc}
     */
    public function keepMessage(MessageInterface $message) : bool
    {
        if ($message->getRoboId() == $this->getRoboId()) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Gets the Message purpose.
     *
     * @return string
     *   The Message purpose.
     */
    public function getRoboId() : string
    {
        return $this->roboId;
    }

}
