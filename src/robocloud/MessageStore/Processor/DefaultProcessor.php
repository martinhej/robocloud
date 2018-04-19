<?php

namespace robocloud\MessageStore\Processor;

use robocloud\MessageStore\Filter\FilterInterface;
use robocloud\MessageStore\Storage\StorageInterface;
use robocloud\MessageStore\Transformer\TransformerInterface;

/**
 * Class DefaultProcessor.
 *
 * @package robocloud\MessageStore\Processor
 */
class DefaultProcessor implements ProcessorInterface {

  /**
   * Message filter object.
   *
   * @var \robocloud\MessageStore\Filter\FilterInterface
   */
  public $filter;

  /**
   * Message transformer object.
   *
   * @var \robocloud\MessageStore\Transformer\TransformerInterface
   */
  public $transformer;

  /**
   * Message storage object.
   *
   * @var \robocloud\MessageStore\Storage\StorageInterface
   */
  public $storage;

  /**
   * DefaultProcessor constructor.
   *
   * @param \robocloud\MessageStore\Filter\FilterInterface $filter
   *   The message filter object.
   * @param \robocloud\MessageStore\Transformer\TransformerInterface $transformer
   *   The message transformer object.
   * @param \robocloud\MessageStore\Storage\StorageInterface $storage
   *   The message storage object.
   */
  public function __construct(FilterInterface $filter, TransformerInterface $transformer, StorageInterface $storage) {
    $this->filter = $filter;
    $this->transformer = $transformer;
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public function processMessages(array $messages) {
    foreach ($messages as $message) {
      if ($this->filter->keepMessage($message)) {
        $this->storage->add($this->transformer->transformMessage($message));
      }
    }

    $this->storage->write();
  }

}
