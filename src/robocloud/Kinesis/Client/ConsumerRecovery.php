<?php

namespace robocloud\Kinesis\Client;

use robocloud\Config\ConfigInterface;
use robocloud\Exception\ConsumerRecoveryException;

/**
 * Class ConsumerRecovery.
 *
 * @package Drupal\acquia_kinesis
 */
class ConsumerRecovery implements ConsumerRecoveryInterface {

  /**
   * @var ConfigInterface
   */
  protected $config;

  /**
   * ConsumerRecovery constructor.
   *
   * @param ConfigInterface $config
   */
  public function __construct(ConfigInterface $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public function hasRecoveryData() {
    $content = $this->getRecoveryFileContent();
    return isset($content[$this->getConfig()->getStreamName()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getLastSequenceNumber($shard_id) {
    $content = $this->getRecoveryFileContent();

    if (isset($content[$this->getConfig()->getStreamName()][$shard_id])) {
      return $content[$this->getConfig()->getStreamName()][$shard_id];
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function storeLastSuccessPosition($shard_id, $sequence_number) {
    $content = $this->getRecoveryFileContent();
    $content[$this->getConfig()->getStreamName()][$shard_id] = $sequence_number;

    $write_result = file_put_contents($this->getConfig()->getRecoveryConsumerRecoveryFile(), json_encode($content));

    if ($write_result === FALSE) {
      throw new ConsumerRecoveryException('Error writing Consumer recovery data at ' . $this->getConfig()->getRecoveryConsumerRecoveryFile());
    }
  }

  /**
   * Gets the recovery file contents.
   *
   * @return array
   *   The recovery file content as array.
   *
   * @throws \robocloud\Exception\ConsumerRecoveryException
   *   On the recovery file read error.
   */
  protected function getRecoveryFileContent() {
    $content = file_get_contents($this->getConfig()->getRecoveryConsumerRecoveryFile());
    if (!empty($content)) {
      return json_decode($content, TRUE);
    }
    elseif ($content === FALSE) {
      throw new ConsumerRecoveryException('Error reading Consumer recovery data at ' . $this->getConfig()->getRecoveryConsumerRecoveryFile());
    }

    return [];
  }

  /**
   * @return ConfigInterface
   */
  public function getConfig() {
    return $this->config;
  }

}
