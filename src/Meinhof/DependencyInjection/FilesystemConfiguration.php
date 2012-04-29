<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class FilesystemConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('meinhof_site');

    $rootNode
        ->children()
            ->arrayNode('globals')
                ->useAttributeAsKey('key')
                ->prototype('scalar')
                ->end()
            ->end()
            ->arrayNode('paths')
                ->children()
                    ->scalarNode('posts')->end()
                ->end()
            ->end()
        ->end()
    ->end();

    return $treeBuilder;
    }
}