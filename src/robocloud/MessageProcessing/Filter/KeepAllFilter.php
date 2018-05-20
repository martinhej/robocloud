<?php

namespace robocloud\MessageProcessing\Filter;

use robocloud\Message\MessageInterface;

/**
 * Class KeepAllFilter.
 *
 * @package robocloud\MessageProcessing\Filter
 */
class KeepAllFilter implements FilterInterface
{

    /**
     * {@inheritdoc}
     */
    public function keepMessage(MessageInterface $message)
    {
        return TRUE;
    }

}
