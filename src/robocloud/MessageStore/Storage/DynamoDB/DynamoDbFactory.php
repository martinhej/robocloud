<?php

namespace robocloud\MessageStore\Storage\DynamoDb;

use Aws\Sdk;

/**
 * Class DynamoDbFactory.
 *
 * @package robocloud\MessageStore\Storage
 */
abstract class DynamoDbFactory {

  /**
   * The instances of this factory.
   *
   * @var \robocloud\MessageStore\Storage\DynamoDb\DynamoDbFactory[]
   */
  protected static $instances = [];

  /**
   * The AWS SDK object.
   *
   * @var \Aws\Sdk
   */
  protected $awsSdk;

  /**
   * The DynamoDB client.
   *
   * @var \Aws\DynamoDb\DynamoDbClient
   */
  protected $dynamoDb;

  /**
   * The DynamoDB table name.
   *
   * @var string
   */
  protected $tableName;

  /**
   * Gets the DynamoDB factory for the given table.
   *
   * @param string $table_name
   *   The DynamoDB table.
   *
   * @return \robocloud\MessageStore\Storage\DynamoDb\DynamoDbFactory
   */
  public static function get($table_name) {
    if (empty(self::$instances[$table_name])) {
      $class = get_called_class();
      self::$instances[$table_name] = new $class($table_name, new Sdk());
    }

    return self::$instances[$table_name];
  }

  /**
   * DynamoDbFactory constructor.
   *
   * @param string $table_name
   *   The table name into which to write.
   * @param \Aws\Sdk $aws_sdk
   *   The AWS SDK.
   */
  public function __construct($table_name, Sdk $aws_sdk) {
    $this->awsSdk = $aws_sdk;
    $this->tableName = $table_name;
  }

  /**
   * Gets the DymamoDB client.
   *
   * @return \Aws\DynamoDb\DynamoDbClient
   *   The DynamoDB client.
   *
   * @throws \RuntimeException
   *   I.e. when wrong API version is provided.
   */
  public function getDynamoDb() {
    if (empty($this->dynamoDb)) {
      $this->dynamoDb = $this->getAwsSdk()->createDynamoDb($this->getConfig());
    }

    return $this->dynamoDb;
  }

  /**
   * Gets the DynamoDB table name.
   *
   * @return string
   *   The table name.
   */
  public function getTableName() {
    return $this->tableName;
  }

  /**
   * Gets the AWS SDK.
   *
   * @return \Aws\Sdk
   *   The Sdk object.
   */
  public function getAwsSdk() {
    return $this->awsSdk;
  }

  /**
   * Gets the DynamoDB configuration.
   *
   * @return array
   *   DynamoDB configuration.
   */
  abstract public function getConfig();

}
