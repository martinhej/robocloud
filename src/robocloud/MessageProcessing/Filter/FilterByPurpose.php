<?php

namespace robocloud\MessageProcessing\Filter;

use robocloud\Message\MessageInterface;

/**
 * Class FilterByPurpose.
 *
 * @package robocloud\MessageProcessing\Filter
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
    public function keepMessage(MessageInterface $message)
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
    public function getPurpose()
    {
        return $this->purpose;
    }

}
