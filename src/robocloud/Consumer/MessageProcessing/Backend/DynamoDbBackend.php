<?php

namespace robocloud\Consumer\MessageProcessing\Backend;

use Aws\Exception\AwsException;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\WriteRequestBatch;
use robocloud\Event\DynamoDb\DynamoDbError;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * DynamoDb robocloud messages storage class.
 */
class DynamoDbBackend implements BackendInterface
{

    /**
     * The write request batch processor.
     *
     * @var \Aws\DynamoDb\WriteRequestBatch
     */
    protected $writeRequestBatch;

    /**
     * DynamoDb constructor.
     *
     * @param \Aws\DynamoDb\DynamoDbClient $client
     *   The DynamoDB client.
     * @param string $stream_name
     *   The stream name which will be used as the table name.
     * @param EventDispatcherInterface $event_dispatcher
     *   The event dispatcher.
     * @param array $write_request_batch_config
     *   Config array as expected by the WriteRequestBatch constructor.
     */
    public function __construct(DynamoDbClient $client, $stream_name, EventDispatcherInterface $event_dispatcher, array $write_request_batch_config = [])
    {

        $write_request_batch_config += [
            'table' => $stream_name,
            'autoflush' => FALSE,
            'error' => function (AwsException $e) use ($event_dispatcher) {
                $event_dispatcher->dispatch(DynamoDbError::NAME, new DynamoDbError($e));
            },
        ];

        $this->writeRequestBatch = new WriteRequestBatch($client, $write_request_batch_config);
    }

    /**
     * Get the write request batch object.
     *
     * @return \Aws\DynamoDb\WriteRequestBatch
     *   A DynamoDb write request batch object.
     */
    public function getWriteRequestBatch()
    {
        return $this->writeRequestBatch;
    }

    /**
     * {@inheritdoc}
     */
    public function add($data)
    {
        $this->writeRequestBatch->put($data);
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $this->getWriteRequestBatch()->flush();
    }

}
