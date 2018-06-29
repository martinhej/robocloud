<?php

namespace robocloud\Kinesis;

use Psr\SimpleCache\CacheInterface;
use robocloud\Message\MessageFactoryInterface;
use Aws\Kinesis\KinesisClient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractKinesisClient.
 *
 * @package robocloud\Kinesis
 */
abstract class AbstractKinesisService implements KinesisServiceInterface
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
     * @param RobocloudKinesisClient $client
     * @param string $stream_name
     * @param MessageFactoryInterface $message_factory
     * @param EventDispatcherInterface $event_dispatcher
     * @param CacheInterface $cache
     */
    public function __construct(RobocloudKinesisClient $client, $stream_name, MessageFactoryInterface $message_factory, EventDispatcherInterface $event_dispatcher, CacheInterface $cache)
    {
        $this->client = $client;
        $this->streamName = $stream_name;
        $this->messageFactory = $message_factory;
        $this->eventDispatcher = $event_dispatcher;
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
        return $this->client->getKinesisClient($this->getType());
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
     * Gets the Kinesis client type.
     *
     * @return string
     */
    abstract function getType();

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

}
