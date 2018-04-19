<?php

namespace robocloud\MessageProcessing\Backend\DynamoDb;

use robocloud\MessageProcessing\Backend\BackendInterface;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\WriteRequestBatch;

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
   * @param array $config
   *   Config array as expected by the WriteRequestBatch constructor.
   */
  public function __construct(DynamoDbClient $client, array $config) {
    $this->writeRequestBatch = new WriteRequestBatch($client, $config);
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
