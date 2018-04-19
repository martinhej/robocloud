<?php

namespace robocloud\MessageStore\Storage\DynamoDb;

use robocloud\MessageStore\Storage\StorageInterface;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\WriteRequestBatch;
use Aws\Exception\AwsException;

/**
 * Class DynamoDb.
 *
 * @package robocloud\MessageStore\Storage
 */
class DynamoDbStorage implements StorageInterface {

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
   * @return \Aws\DynamoDb\WriteRequestBatch
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
  public function write() {
    $this->getWriteRequestBatch()->flush();
  }

}
