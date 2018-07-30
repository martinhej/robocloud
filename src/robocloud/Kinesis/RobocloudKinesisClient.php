<?php

namespace robocloud\Kinesis;

use Aws\Sdk;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RobocloudKinesisClient {

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $clients = [];

    /**
     * RobocloudKinesisClient constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->getParameter('robocloud')['kinesis'];
    }

    /**
     * Gets the Kinesis client.
     *
     * @param string $type
     *   The client type [producer, consumer].
     *
     * @return \Aws\Kinesis\KinesisClient
     */
    public function getKinesisClient($type)
    {

        if (!empty($this->clients[$type])) {
            return $this->clients[$type];
        }

        $config = [
            'version' => $this->config['api_version'],
            'region' => $this->config['region'],
        ];

        $config['credentials'] = [
            'key' => $this->config[$type]['key'],
            'secret' => $this->config[$type]['secret'],
        ];

        $sdk = new Sdk();
        $this->clients[$type] = $sdk->createKinesis($config);

        return $this->clients[$type];
    }
}