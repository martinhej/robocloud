<?php

namespace robocloud\Config;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class DefaultConfig implements ConfigInterface {

  protected static $instance;

  protected $messageSchemaDir;

  protected $messageSchemaVersion;

  /**
   * DefaultConfig constructor.
   *
   * @throws ParseException If the YAML is not valid.
   */
  public function __construct() {
    $yaml = Yaml::parseFile('conf/conf.yml');
    $this->messageSchemaDir = $yaml['message-schema-dir'];
    $this->messageSchemaVersion = $yaml['message-schema-version'];
  }

  public function getMessageSchemaDir() {
    return $this->messageSchemaDir;
  }

  public function getMessageSchemaVersion() {
    return $this->messageSchemaVersion;
  }

}
