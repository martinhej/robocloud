<?php

namespace robocloud\Tests\Consumer\Kinesis;

use Aws\AwsClient;
use Aws\Result;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use robocloud\Consumer\Kinesis\Consumer;
use robocloud\Consumer\MessageProcessing\Backend\KeepInMemoryBackend;
use robocloud\Consumer\MessageProcessing\Filter\KeepAllFilter;
use robocloud\Consumer\MessageProcessing\Processor\DefaultProcessor;
use robocloud\Consumer\MessageProcessing\Transformer\KeepOriginalTransformer;
use robocloud\Kinesis\ConsumerRecoveryInterface;
use robocloud\Kinesis\RobocloudKinesisClient;
use robocloud\Message\RoboMessage;
use robocloud\Message\MessageFactory;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Tests the consumer part.
 *
 * @todo - needs edge cases coverage.
 */
class ConsumerTest extends TestCase
{

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var MockObject
     */
    protected $robocloudKinesisClient;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var MockObject
     */
    protected $consumerRecovery;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->messageFactory = new MessageFactory(RoboMessage::class, $this->eventDispatcher);
        $this->cache = new ArrayCache();

        $this->robocloudKinesisClient = $this->getMockBuilder(RobocloudKinesisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['getKinesisClient'])
            ->getMock();
        $this->consumerRecovery = $this->getMockBuilder(ConsumerRecoveryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'setShardId',
                'hasRecoveryData',
                'getLastSuccessPosition',
                'storeLastSuccessPosition',
            ])
            ->getMock();
    }

    /**
     * Tests basic logic flow when consuming.
     */
    public function testConsume() {

        $message = new RoboMessage([
            'messageId' => '123',
            'version' => 'v_0_1',
            'messageTime' => '',
            'roboId' => 'test',
            'purpose' => 'buddy.find',
            'data' => ['reason' => 'lost in space'],
        ]);
        $message_data = $this->messageFactory->serialize($message);

        $shard_result = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->setMethods(['search'])
            ->getMock();
        $shard_result->expects($this->once())
            ->method('search')
            ->with('StreamDescription.Shards[].ShardId')
            ->willReturn(['Shard-00001']);

        $iterator_result = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $iterator_result->expects($this->once())
            ->method('get')
            ->with('ShardIterator')
            ->willReturn('initialIterator');

        $messages_result = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'search'])
            ->getMock();
        $messages_result->expects($this->exactly(2))
            ->method('get')
            ->with($this->logicalOr(
                $this->equalTo('NextShardIterator'),
                $this->equalTo('MillisBehindLatest')
            ))
            ->will($this->returnCallback(function ($arg)
            {
                if ($arg == 'NextShardIterator') {
                    return '1234567890qwerty';
                }
                elseif ($arg == 'MillisBehindLatest') {
                    return 10;
                }

                return null;
            }));
        $messages_result->expects($this->once())
            ->method('search')
            ->with('Records[].[SequenceNumber, Data]')
            ->willReturn([[123, $message_data]]);

        $aws_client = $this->getMockBuilder(AwsClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['describeStream', 'getShardIterator', 'getRecords'])
            ->getMock();
        $aws_client->expects($this->once())
            ->method('describeStream')
            ->with(['StreamName' => 'test_stream'])
            ->willReturn($shard_result);
        $aws_client->expects($this->once())
            ->method('getShardIterator')
            ->with([
                'ShardId' => 'Shard-00001',
                'StreamName' => 'test_stream',
                'ShardIteratorType' => 'TRIM_HORIZON',
            ])
            ->willReturn($iterator_result);
        $aws_client->expects($this->once())
            ->method('getRecords')
            ->with([
                'Limit' => 10,
                'ShardIterator' => 'initialIterator',
                'StreamName' => 'test_stream',
            ])
            ->willReturn($messages_result);

        $this->robocloudKinesisClient
            ->expects($this->exactly(3))
            ->method('getKinesisClient')
            ->with('consumer')
            ->willReturn($aws_client);

        $this->consumerRecovery
            ->expects($this->once())
            ->method('setShardId')
            ->with('Shard-00001');
        $this->consumerRecovery
            ->expects($this->once())
            ->method('hasRecoveryData')
            ->willReturn(false);

        $backend = new KeepInMemoryBackend();
        $processor = new DefaultProcessor(new KeepAllFilter(), new KeepOriginalTransformer(), $backend);
        $this->eventDispatcher->addSubscriber($processor);

        $consumer = new Consumer(
            $this->robocloudKinesisClient,
            'test_stream',
            $this->messageFactory,
            $this->eventDispatcher,
            $this->cache,
            $this->consumerRecovery
        );

        $consumer->consume(0, 'Shard-00001');

        /** @var RoboMessage[] $messages */
        $messages = $backend->flush();

        $this->assertEquals('v_0_1', $messages[0]->getVersion());
        $this->assertEquals('123', $messages[0]->getMessageId());
        $this->assertEquals('buddy.find', $messages[0]->getPurpose());
        $this->assertEquals('test', $messages[0]->getRoboId());
        $this->assertEquals('lost in space', $messages[0]->getData()['reason']);
    }

}
