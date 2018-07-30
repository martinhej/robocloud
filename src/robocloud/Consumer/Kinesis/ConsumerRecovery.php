<?php

namespace robocloud\Kinesis;

use robocloud\Config\ConfigInterface;
use robocloud\Exception\ConsumerRecoveryException;
use robocloud\Exception\ShardInitiationException;

/**
 * Class ConsumerRecovery.
 *
 * @package Drupal\acquia_kinesis
 */
class ConsumerRecovery implements ConsumerRecoveryInterface
{

    /**
     * @var string
     */
    protected $streamName;

    /**
     * @var string
     */
    protected $shardId;

    /**
     * @var string
     */
    protected $consumerRecoveryFile;

    /**
     * ConsumerRecovery constructor.
     *
     * @param string $stream_mame
     * @param string $consumer_recovery_file
     */
    public function __construct($stream_mame, $consumer_recovery_file)
    {
        $this->streamName = $stream_mame;
        $this->consumerRecoveryFile = $consumer_recovery_file;
    }

    /**
     * Sets the shard id.
     *
     * @param string $shard_id
     */
    public function setShardId($shard_id)
    {
        $this->shardId = $shard_id;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRecoveryData(): bool
    {
        $content = $this->getRecoveryFileContent();
        return isset($content[$this->streamName]);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastSuccessPosition(): string
    {
        $content = $this->getRecoveryFileContent();

        if (isset($content[$this->streamName][$this->shardId])) {
            return $content[$this->streamName][$this->shardId];
        }

        return NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function storeLastSuccessPosition($position)
    {
        $content = $this->getRecoveryFileContent();
        $content[$this->streamName][$this->shardId] = $position;

        $write_result = file_put_contents($this->consumerRecoveryFile, json_encode($content));

        if ($write_result === FALSE) {
            throw new ConsumerRecoveryException('Error writing Consumer recovery data at ' . $this->consumerRecoveryFile);
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
    protected function getRecoveryFileContent(): array
    {
        $content = file_get_contents($this->consumerRecoveryFile);
        if (!empty($content)) {
            return json_decode($content, TRUE);
        } elseif ($content === FALSE) {
            throw new ConsumerRecoveryException('Error reading Consumer recovery data at ' . $this->consumerRecoveryFile);
        }

        return [];
    }

}
