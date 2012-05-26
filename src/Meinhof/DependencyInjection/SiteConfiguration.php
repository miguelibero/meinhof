<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class SiteConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('site');

        $rootNode
            ->children()
                ->arrayNode('info')
                    ->useAttributeAsKey('key')
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('urls')
                    ->children()
                        ->scalarNode('post')->end()
                        ->scalarNode('page')->end()
                        ->scalarNode('public')->end()
                        ->scalarNode('web')->end()
                        ->scalarNode('content')->end()
                    ->end()
                ->end()
                ->arrayNode('post')
                    ->children()
                        ->scalarNode('view')->end()
                        ->arrayNode('info')
                            ->useAttributeAsKey('key')
                            ->prototype('variable')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}

