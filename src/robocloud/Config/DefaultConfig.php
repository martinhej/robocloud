<?php

namespace robocloud\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class DefaultConfig implements ConfigInterface {

  protected $streamName;

  protected $messageSchemaDir;

  protected $messageSchemaVersion;

  protected $recoveryConsumerRecoveryFile;

  /**
   * DefaultConfig constructor.
   *
   * @throws ParseException If the YAML is not valid.
   */
  public function __construct() {
    $yaml = Yaml::parseFile('conf/conf.yml');
    $this->streamName = $yaml['stream-name'];
    $this->messageSchemaDir = $yaml['message-schema-dir'];
    $this->messageSchemaVersion = $yaml['message-schema-version'];
    $this->recoveryConsumerRecoveryFile = $yaml['consumer-recovery-file'];
  }

  public function getStreamName() {
    return $this->streamName;
  }

  public function getMessageSchemaDir() {
    return $this->messageSchemaDir;
  }

  public function getMessageSchemaVersion() {
    return $this->messageSchemaVersion;
  }

  public function getRecoveryConsumerRecoveryFile() {
    return $this->recoveryConsumerRecoveryFile;
  }

}
