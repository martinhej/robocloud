<?php

namespace robocloud;

use Aws\Sdk;

/**
 * Class DynamoDbClientFactory.
 *
 * @package robocloud
 */
class DynamoDbClientFactory
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
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

    /**
     * KinesisClientFactory constructor.
     *
     * @param string $api_version
     *   The Kinesis API version.
     * @param string $region
     *   The AWS region.
     * @param string $key
     *   The IAM user key.
     * @param string $secret
     *   The IAM user secret.
     */
    public function __construct($api_version, $region, $key, $secret)
    {
        $this->apiVersion = $api_version;
        $this->region = $region;
        $this->key = $key;
        $this->secret = $secret;
    }

    public function getDynamoDbClient()
    {
        $config = [
            'version' => $this->apiVersion,
            'region' => $this->region,
            'credentials' => [
                'key' => $this->key,
                'secret' => $this->secret,
            ]
        ];

        $sdk = new Sdk();
        return $sdk->createDynamoDb($config);
    }

}
