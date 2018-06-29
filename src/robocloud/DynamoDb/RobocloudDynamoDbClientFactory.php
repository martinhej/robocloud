<?php

namespace robocloud\DynamoDb;

use Aws\DynamoDb\DynamoDbClient;
use Aws\Sdk;
use Symfony\Component\DependencyInjection\Container;

/**
 * DynamoDb client factory.
 */
class RobocloudDynamoDbClientFactory {

    /**
     * @var array
     */
    protected $config;

    /**
     * RobocloudDynamoDbClient constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->config = $container->getParameter('robocloud')['dynamodb'];
    }

    /**
     * @return \Aws\DynamoDb\DynamoDbClient
     */
    public function getDynamoDbClient() : DynamoDbClient
    {
        $config = [
            'version' => $this->config['api_version'],
            'region' => $this->config['region'],
            'credentials' => [
                'key' => $this->config['key'],
                'secret' => $this->config['secret'],
            ]
        ];

        $sdk = new Sdk();
        return $sdk->createDynamoDb($config);
    }
}