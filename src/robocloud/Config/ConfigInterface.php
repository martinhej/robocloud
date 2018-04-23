<?php

namespace robocloud\Config;

interface ConfigInterface {

  public function getStreamName();

  public function getMessageSchemaDir();

  public function getMessageSchemaVersion();

  public function getRecoveryConsumerRecoveryFile();

  public function getDynamoDbApiVersion();

  public function getDynamoDbRegion();

  public function getKinesisApiVersion();

  public function getKinesisRegion();

  public function getKinesisConsumerKey();

  public function getKinesisConsumerSecret();

  public function getKinesisProducerKey();

  public function getKinesisProducerSecret();

}
