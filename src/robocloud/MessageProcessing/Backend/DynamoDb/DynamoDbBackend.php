<?php

namespace robocloud\MessageProcessing\Backend\DynamoDb;

use Aws\Exception\AwsException;
use robocloud\Event\DynamoDbError;
use robocloud\MessageProcessing\Backend\BackendInterface;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\WriteRequestBatch;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DynamoDb.
 *
 * @package robocloud\MessageProcessing\Storage
 */
class DynamoDbBackend implements BackendInterface {

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
   * @param string $streamName
   *   The stream name which will be used as the table name.
   * @param EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   * @param array $writeRequestBatchConfig
   *   Config array as expected by the WriteRequestBatch constructor.
   */
  public function __construct(DynamoDbClient $client, $streamName, EventDispatcherInterface $eventDispatcher, array $writeRequestBatchConfig = []) {

    $writeRequestBatchConfig += [
      'table' => $streamName,
      'autoflush' => FALSE,
      'error' => function (AwsException $e) use ($eventDispatcher) {
        $eventDispatcher->dispatch(DynamoDbError::NAME, new DynamoDbError($e));
      },
    ];

    $this->writeRequestBatch = new WriteRequestBatch($client, $writeRequestBatchConfig);
  }

  /**
   * Get the write request batch object.
   *
   * @return \Aws\DynamoDb\WriteRequestBatch
   *   A DynamoDb write request batch object.
   */
  public function getWriteRequestBatch() {
    return $this->writeRequestBatch;
  }

  /**
   * {@inheritdoc}
   */
  public function add(array $data) {
    $this->writeRequestBatch->put($data);
  }

  /**
   * {@inheritdoc}
   */
  public function process() {
    $this->getWriteRequestBatch()->flush();
  }

}
