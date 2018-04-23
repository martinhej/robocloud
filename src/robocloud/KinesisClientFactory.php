<?php

namespace robocloud;

use Aws\AwsClient;
use Aws\Sdk;
use robocloud\Config\ConfigInterface;

/**
 * Class KinesisClientFactory.
 *
 * @package robocloud
 */
class KinesisClientFactory {

  protected $config;

  /**
   * KinesisClientFactory constructor.
   *
   * @param ConfigInterface $config
   */
  public function __construct(ConfigInterface $config) {
    $this->config = $config;
  }

  /**
   * Gets the Kinesis client.
   *
   * @param string $type
   *   Either "producer" or "consumer".
   *
   * @return \Aws\Kinesis\KinesisClient
   */
  public function getKinesisClient($type) {
    $config = [
      'version' => $this->config->getKinesisApiVersion(),
      'region' => $this->config->getKinesisRegion(),
    ];

    if ($type == 'producer') {
      $config['credentials'] = [
        'key'    => $this->config->getKinesisProducerKey(),
        'secret' => $this->config->getKinesisProducerSecret(),
      ];
    }
    elseif ($type == 'consumer') {
      $config['credentials'] = [
        'key'    => $this->config->getKinesisConsumerKey(),
        'secret' => $this->config->getKinesisConsumerSecret(),
      ];
    }

    $sdk = new Sdk();
    return $sdk->createKinesis($config);
  }

}
