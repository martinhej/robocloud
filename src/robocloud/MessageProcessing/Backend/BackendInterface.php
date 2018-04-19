<?php

namespace robocloud\MessageProcessing\Backend;

/**
 * Interface BackendInterface.
 *
 * Takes any required action.
 *
 * @package robocloud\MessageProcessing\Storage
 */
interface BackendInterface {

  /**
   * Adds processed message data for being stored.
   *
   * @param array $data
   *   Message data returned by the TransformerInterface implementation.
   */
  public function add(array $data);

  /**
   * Takes any required action upon receiving robocloud data.
   */
  public function process();

}
