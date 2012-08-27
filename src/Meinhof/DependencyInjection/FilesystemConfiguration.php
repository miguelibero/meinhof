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
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function($v) {
                                return array('key' => $v);
                            })
                        ->end()
                        ->children()
                            ->scalarNode('key')->end()
                            ->arrayNode('info')
                                ->useAttributeAsKey('key')
                                ->prototype('variable')->end()
                            ->end()
                            ->scalarNode('view')->end()
                            ->scalarNode('slug')->end()
                            ->scalarNode('url')->end()
                            ->scalarNode('updated')->end()
                            ->scalarNode('title')->end()
                            ->scalarNode('publish')->end()
                            ->arrayNode('categories')
                                ->prototype('scalar')->end()
                            ->end()
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
