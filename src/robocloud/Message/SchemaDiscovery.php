<?php

namespace robocloud\Message;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use robocloud\Exception\InvalidMessageDataException;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Discovers message schemas.
 */
class SchemaDiscovery implements SchemaDiscoveryInterface
{
    /**
     * Schema directories.
     *
     * @var array
     */
    protected $schemaDirs = [];

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * SchemaDiscovery constructor.
     *
     * @param ContainerInterface $container
     * @param CacheInterface $cache
     */
    public function __construct(ContainerInterface $container, CacheInterface $cache)
    {
        $this->schemaDirs = $container->getParameter('robocloud')['message_schema_dirs'];
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneralMessageSchema(): \stdClass
    {
        $file_locator = new FileLocator(__DIR__ . '/../Resources/schema');
        $path = $file_locator->locate('message.schema.json');

        return json_decode(file_get_contents($path));
    }

    /**
     * Gets message schema.
     *
     * @param MessageInterface $message
     *   The message for which to get schema.
     *
     * @return object
     *   The message data property schema.
     *
     * @throws InvalidMessageDataException
     * @throws InvalidArgumentException
     */
    public function getMessageDataSchema(MessageInterface $message): \stdClass
    {
        $parts = explode('.', $message->getPurpose());

        if (empty($parts)) {
            throw new InvalidMessageDataException('The message "purpose" property should clearly define specific message schema');
        }

        if ($this->cache->has($message->getPurpose())) {
            return $this->cache->get($message->getPurpose());
        }

        $file_name = array_pop($parts) . '.schema.json';

        $purpose_dirs = '';
        if (!empty($parts)) {
            $purpose_dirs = implode(DIRECTORY_SEPARATOR, $parts);
        }

        $schema_dirs = array_filter(array_map(function($dir) use ($purpose_dirs, $message) {
            $schema_dir = $dir . DIRECTORY_SEPARATOR . $message->getVersion() . DIRECTORY_SEPARATOR . $purpose_dirs;
            if (file_exists($schema_dir)) {
                return $schema_dir;
            }
            return NULL;
        }, $this->schemaDirs));

        $file_locator = new FileLocator($schema_dirs);

        try {
            $path = $file_locator->locate($file_name);
            $schema = json_decode(file_get_contents($path));

            $this->cache->set($message->getPurpose(), $schema);

            return $schema;
        }
        catch (FileLocatorFileNotFoundException $e) {
            throw new InvalidMessageDataException('Could not find message with purpose "' . $message->getPurpose() . '"');
        }
    }

}
