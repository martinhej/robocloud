<?php

namespace robocloud\Consumer\MessageProcessing\Processor;

use robocloud\Event\MessagesConsumedEvent;
use robocloud\Consumer\MessageProcessing\Filter\FilterInterface;
use robocloud\Consumer\MessageProcessing\Backend\BackendInterface;
use robocloud\Consumer\MessageProcessing\Transformer\TransformerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Default processor to process consumed messages.
 */
class DefaultProcessor implements ProcessorInterface, EventSubscriberInterface
{

    /**
     * Message filter object.
     *
     * @var \robocloud\Consumer\MessageProcessing\Filter\FilterInterface
     */
    public $filter;

    /**
     * Message transformer object.
     *
     * @var \robocloud\Consumer\MessageProcessing\Transformer\TransformerInterface
     */
    public $transformer;

    /**
     * Message storage object.
     *
     * @var \robocloud\Consumer\MessageProcessing\Backend\BackendInterface
     */
    public $backend;

    /**
     * DefaultProcessor constructor.
     *
     * @param \robocloud\Consumer\MessageProcessing\Filter\FilterInterface $filter
     *   The message filter object.
     * @param \robocloud\Consumer\MessageProcessing\Transformer\TransformerInterface $transformer
     *   The message transformer object.
     * @param \robocloud\Consumer\MessageProcessing\Backend\BackendInterface $backend
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
    public static function getSubscribedEvents() : array
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
