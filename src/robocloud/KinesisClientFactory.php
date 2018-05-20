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
class KinesisClientFactory
{

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var string
     */
    protected $region;

    /**
     * KinesisClientFactory constructor.
     *
     * @param string $api_version
     *   The Kinesis API version.
     * @param string $region
     *   The AWS region.
     */
    public function __construct($api_version, $region)
    {
        $this->apiVersion = $api_version;
        $this->region = $region;
    }

    /**
     * Gets the Kinesis client.
     *
     * @param string $key
     *   The IAM user key.
     * @param string $secret
     *   The IAM user secret.
     *
     * @return \Aws\Kinesis\KinesisClient
     */
    public function getKinesisClient($key, $secret) {
        $config = [
            'version' => $this->apiVersion,
            'region' => $this->region,
        ];

        $config['credentials'] = [
            'key' => $key,
            'secret' => $secret,
        ];

        $sdk = new Sdk();
        return $sdk->createKinesis($config);
    }

}
