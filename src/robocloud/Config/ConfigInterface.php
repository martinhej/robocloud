<?php

namespace robocloud\Config;

interface ConfigInterface {

  public function getStreamName();

  public function getMessageSchemaDir();

  public function getMessageSchemaVersion();

  public function getRecoveryConsumerRecoveryFile();

}
