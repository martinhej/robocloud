<?php

namespace robocloud\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class DefaultConfig implements ConfigInterface {

  protected $yaml = [];

  /**
   * DefaultConfig constructor.
   *
   * @throws ParseException If the YAML is not valid.
   */
  public function __construct() {
    $this->yaml = Yaml::parseFile('config/robocloud.yml');
  }

  public function getStreamName() {
    return $this->yaml['stream-name'];
  }

  public function getMessageSchemaDir() {
    return $this->yaml['message-schema-dir'];
  }

  public function getMessageSchemaVersion() {
    return $this->yaml['message-schema-version'];
  }

  public function getRecoveryConsumerRecoveryFile() {
    return $this->yaml['consumer-recovery-file'];
  }

  public function getKinesisApiVersion() {
    return $this->yaml['kinesis-api-version'];
  }

  public function getKinesisRegion() {
    return $this->yaml['kinesis-region'];
  }

  public function getKinesisConsumerKey() {
    return $this->yaml['kinesis-consumer-key'];
  }

  public function getKinesisConsumerSecret() {
    return $this->yaml['kinesis-consumer-secret'];
  }

  public function getKinesisProducerKey() {
    return $this->yaml['kinesis-producer-key'];
  }

  public function getKinesisProducerSecret() {
    return $this->yaml['kinesis-producer-secret'];
  }

  public function getDynamoDbApiVersion() {
    return $this->yaml['dynamodb-api-version'];
  }

  public function getDynamoDbRegion() {
    return $this->yaml['dynamodb-region'];
  }


}
