<?php

namespace robocloud\MessageProcessing\Processor;

use robocloud\Event\MessagesConsumedEvent;
use robocloud\MessageProcessing\Filter\FilterInterface;
use robocloud\MessageProcessing\Backend\BackendInterface;
use robocloud\MessageProcessing\Transformer\TransformerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DefaultProcessor.
 *
 * @package robocloud\MessageProcessing\Processor
 */
class DefaultProcessor implements ProcessorInterface, EventSubscriberInterface
{

    /**
     * Message filter object.
     *
     * @var \robocloud\MessageProcessing\Filter\FilterInterface
     */
    public $filter;

    /**
     * Message transformer object.
     *
     * @var \robocloud\MessageProcessing\Transformer\TransformerInterface
     */
    public $transformer;

    /**
     * Message storage object.
     *
     * @var \robocloud\MessageProcessing\Backend\BackendInterface
     */
    public $backend;

    /**
     * DefaultProcessor constructor.
     *
     * @param \robocloud\MessageProcessing\Filter\FilterInterface $filter
     *   The message filter object.
     * @param \robocloud\MessageProcessing\Transformer\TransformerInterface $transformer
     *   The message transformer object.
     * @param \robocloud\MessageProcessing\Backend\BackendInterface $backend
     *   The message storage object.
     */
    public function __construct(FilterInterface $filter, TransformerInterface $transformer, BackendInterface $backend)
    {
        $this->filter = $filter;
        $this->transformer = $transformer;
        $this->backend = $backend;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            MessagesConsumedEvent::NAME => 'processMessages',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function processMessages(MessagesConsumedEvent $event)
    {

        $messages = $event->getMessages();

        foreach ($messages as $message) {
            if ($this->filter->keepMessage($message)) {
                $this->backend->add($this->transformer->transformMessage($message));
            }
        }

        $this->backend->process();
    }

}
