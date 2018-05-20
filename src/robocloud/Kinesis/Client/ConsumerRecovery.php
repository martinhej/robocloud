<?php

namespace robocloud\Kinesis\Client;

use robocloud\Config\ConfigInterface;
use robocloud\Exception\ConsumerRecoveryException;

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
     * {@inheritdoc}
     */
    public function hasRecoveryData()
    {
        $content = $this->getRecoveryFileContent();
        return isset($content[$this->getStreamName()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastSequenceNumber($shard_id)
    {
        $content = $this->getRecoveryFileContent();

        if (isset($content[$this->getStreamName()][$shard_id])) {
            return $content[$this->getStreamName()][$shard_id];
        }

        return NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function storeLastSuccessPosition($shard_id, $sequence_number)
    {
        $content = $this->getRecoveryFileContent();
        $content[$this->getStreamName()][$shard_id] = $sequence_number;

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
    protected function getRecoveryFileContent()
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
    public function getStreamName()
    {
        return $this->streamName;
    }

    /**
     * @return string
     */
    public function getConsumerRecoveryFile(): string
    {
        return $this->consumerRecoveryFile;
    }

}
