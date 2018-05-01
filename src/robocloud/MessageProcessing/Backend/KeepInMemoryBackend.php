<?php

namespace robocloud\MessageProcessing\Backend;

/**
 * Class KeepInMemoryBackend.
 *
 * @package robocloud\MessageProcessing\Backend
 */
class KeepInMemoryBackend implements BackendInterface {

  protected $messages = [];

  /**
   * {@inheritdoc}
   */
  public function add($data) {
    $this->messages[] = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function process() {

  }

  /**
   * Gets Messages and flushes the queue.
   *
   * @return array
   *   The Messages.
   */
  public function flush() {
    $messages = $this->messages;
    $this->messages = [];

    return $messages;
  }

}
