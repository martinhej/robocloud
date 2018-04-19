<?php

namespace robocloud\MessageStore\Storage;

/**
 * Interface StorageInterface.
 *
 * @package robocloud\MessageStore\Storage
 */
interface StorageInterface {

  /**
   * Adds processed message data for being stored.
   *
   * @param array $data
   *   Message data returned by the TransformerInterface implementation.
   */
  public function add(array $data);

  /**
   * Writes added message data.
   *
   * @return mixed
   *   The response from the storage client.
   */
  public function write();

}
