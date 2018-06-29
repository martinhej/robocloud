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
class KinesisConsumerRecovery implements ConsumerRecoveryInterface
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
     * @param array $data
     * @return ConsumerRecoveryInterface
     * @throws \InvalidArgumentException
     */
    public function setConsumerData(array $data) : ConsumerRecoveryInterface
    {
        if (empty($data['shardId']) || !is_scalar($this->shardId) || strpos($this->shardId, 'Shard') === false) {
            throw new \InvalidArgumentException('The Kinesis Consumer Recovery expects "shardId" in the Consumer data');
        }

        $this->shardId = $data['shardId'];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRecoveryData() : bool
    {
        $content = $this->getRecoveryFileContent();
        return isset($content[$this->getStreamName()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastSequenceNumber() : string
    {
        $content = $this->getRecoveryFileContent();

        if (isset($content[$this->getStreamName()][$this->getShardId()])) {
            return $content[$this->getStreamName()][$this->getShardId()];
        }

        return NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function storeLastSuccessPosition($sequence_number)
    {
        $content = $this->getRecoveryFileContent();
        $content[$this->getStreamName()][$this->getShardId()] = $sequence_number;

        $write_result = file_put_contents($this->getConsumerRecoveryFile(), json_encode($content));

        if ($write_result === FALSE) {
            throw new ConsumerRecoveryException('Error writing Consumer recovery data at ' . $this->getConsumerRecoveryFile());
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
    protected function getRecoveryFileContent() : array
    {
        $content = file_get_contents($this->getConsumerRecoveryFile());
        if (!empty($content)) {
            return json_decode($content, TRUE);
        } elseif ($content === FALSE) {
            throw new ConsumerRecoveryException('Error reading Consumer recovery data at ' . $this->getConsumerRecoveryFile());
        }

        return [];
    }

    /**
     * @return string
     */
    public function getStreamName() : string
    {
        return $this->streamName;
    }

    /**
     * @return string
     */
    public function getShardId() : string
    {
        return $this->shardId;
    }

    /**
     * @return string
     */
    public function getConsumerRecoveryFile() : string
    {
        return $this->consumerRecoveryFile;
    }

}
