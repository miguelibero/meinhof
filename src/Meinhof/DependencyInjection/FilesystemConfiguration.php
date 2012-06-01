<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class FilesystemConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('filesystem');

        $rootNode
            ->children()
                ->arrayNode('paths')
                    ->children()
                        ->scalarNode('base')->end()
                        ->scalarNode('posts')->end()
                        ->scalarNode('views')->end()
                        ->scalarNode('web')->end()
                        ->scalarNode('public')->end()
                        ->scalarNode('translations')->end()
                    ->end()
                ->end()
                ->arrayNode('pages')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function($v) {
                                return array('key' => $v);
                            })
                        ->end()
                        ->children()
                            ->scalarNode('key')->end()
                            ->scalarNode('path')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('assets')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function($v) {
                                return array('source' => $v);
                            })
                        ->end()
                        ->children()
                            ->scalarNode('source')->end()
                            ->scalarNode('filter')->end()
                            ->scalarNode('output')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('categories')
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('slug')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
