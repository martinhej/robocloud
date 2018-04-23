<?php

namespace robocloud;

use Aws\Sdk;
use robocloud\Config\ConfigInterface;

class DynamoDbClientFactory {

  protected $config;

  /**
   * KinesisClientFactory constructor.
   *
   * @param ConfigInterface $config
   */
  public function __construct(ConfigInterface $config) {
    $this->config = $config;
  }

  public function getDynamoDbClient() {
    $config = [
      'version'     => $this->config->getDynamoDbApiVersion(),
      'region'      => $this->config->getDynamoDbRegion(),
      'credentials' => [
        'key'    => $this->config->getKinesisConsumerKey(),
        'secret' => $this->config->getKinesisConsumerSecret(),
      ]
    ];

    $sdk = new Sdk();
    return $sdk->createDynamoDb($config);
  }

}
