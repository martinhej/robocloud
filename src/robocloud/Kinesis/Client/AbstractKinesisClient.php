<?php

namespace robocloud\Kinesis\Client;

use Psr\SimpleCache\CacheInterface;
use robocloud\Message\MessageFactoryInterface;
use Aws\Kinesis\KinesisClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractKinesisClient.
 *
 * @package robocloud\Kinesis
 */
abstract class AbstractKinesisClient implements ClientInterface
{

    /**
     * The Kinesis client object.
     *
     * @var \Aws\Kinesis\KinesisClient
     */
    protected $client;

    /**
     * The stream name.
     *
     * @var string
     */
    protected $streamName;

    /**
     * @var MessageFactoryInterface
     */
    protected $messageFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * AbstractClient constructor.
     *
     * @param \Aws\Kinesis\KinesisClient $client
     *   The Kinesis client object.
     * @param string $streamName
     *   The stream name.
     * @param MessageFactoryInterface $messageFactory
     * @param EventDispatcherInterface $eventDispatcher
     * @param CacheInterface $cache
     */
    public function __construct(KinesisClient $client, $streamName, MessageFactoryInterface $messageFactory, EventDispatcherInterface $eventDispatcher, CacheInterface $cache)
    {
        $this->client = $client;
        $this->streamName = $streamName;
        $this->messageFactory = $messageFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->cache = $cache;
    }

    /**
     * Gets the stream name.
     *
     * @return string
     *   The stream name.
     */
    public function getStreamName()
    {
        return $this->streamName;
    }

    /**
     * Gets the Kinesis client.
     *
     * @return \Aws\Kinesis\KinesisClient
     *   The Kinesis client.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Gets shard ids.
     *
     * @return int[]
     *   List of shard ids within the stream.
     */
    public function getShardIds()
    {
        $key = $this->getStreamName() . '.shard_ids';

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $res = $this->getClient()->describeStream(['StreamName' => $this->getStreamName()]);
        $shard_ids = $res->search('StreamDescription.Shards[].ShardId');
        // Cache shard_ids for a day as overuse of the describeStream
        // resource results in LimitExceededException error.
        $this->cache->set($key, $shard_ids, 86400);

        return $shard_ids;
    }

    /**
     * Gets the message factory.
     *
     * @return \robocloud\Message\MessageFactoryInterface
     *   The message factory.
     */
    public function getMessageFactory()
    {
        return $this->messageFactory;
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

}
