<?php

namespace robocloud\Consumer\MessageProcessing\Filter;

use robocloud\Message\MessageInterface;

/**
 * Filters consumed messages by the message purpose property.
 */
class FilterByPurpose implements FilterInterface
{

    /**
     * The Message purpose.
     *
     * @var string
     */
    protected $purpose;

    /**
     * FilterByPurpose constructor.
     *
     * @param string $purpose
     *   The Message purpose.
     */
    public function __construct($purpose)
    {
        $this->purpose = $purpose;
    }

    /**
     * {@inheritdoc}
     */
    public function keepMessage(MessageInterface $message) : bool
    {
        if ($message->getPurpose() == $this->getPurpose()) {
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
    public function getPurpose() : string
    {
        return $this->purpose;
    }

}
