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
                    ->end()
                ->end()
                ->arrayNode('pages')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('categories')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
