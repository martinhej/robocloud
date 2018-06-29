<?php

namespace robocloud\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @package robocloud\DependencyInjection
 */
class Configuration implements ConfigurationInterface {

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('robocloud');

        $rootNode
            ->children()
                ->scalarNode('stream_name')->isRequired()->end()
                ->scalarNode('message_schema_dir')->isRequired()->end()
                ->scalarNode('message_class')->defaultValue('robocloud\Message\Message')->end()
            ->end();

        $this->addKinesisConfiguration($rootNode);
        $this->addDynamoDBConfiguration($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds Kinesis configuration.
     *
     * @param ArrayNodeDefinition $rootNode
     */
    protected function addKinesisConfiguration(ArrayNodeDefinition $rootNode) {
        $rootNode->children()
            ->arrayNode('kinesis')
                ->children()
                    ->scalarNode('api_version')->defaultValue('2013-12-02')->end()
                    ->scalarNode('region')->isRequired()->end()
                    ->arrayNode('consumer')
                        ->children()
                            ->scalarNode('recovery_file')->isRequired()->end()
                            ->scalarNode('key')->isRequired()->end()
                            ->scalarNode('secret')->isRequired()->end()
                        ->end()
                    ->end()
                    ->arrayNode('producer')
                        ->children()
                            ->scalarNode('key')->isRequired()->end()
                            ->scalarNode('secret')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * Adds DynamoDb configuration.
     *
     * @param ArrayNodeDefinition $rootNode
     */
    protected function addDynamoDBConfiguration(ArrayNodeDefinition $rootNode) {
        $rootNode->children()
            ->arrayNode('dynamodb')
                ->children()
                    ->scalarNode('api_version')->defaultValue('2012-08-10')->end()
                    ->scalarNode('region')->isRequired()->end()
                ->end()
            ->end()
        ->end();
    }
}