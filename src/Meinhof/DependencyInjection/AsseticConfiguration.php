<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class AsseticConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('assetic');

        $rootNode
            ->children()
                ->arrayNode('filters')
                    ->useAttributeAsKey('key')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('assets')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function($v) {
                                return array('input' => $v);
                            })
                        ->end()
                        ->children()
                            ->scalarNode('input')->end()
                            ->scalarNode('filter')->end()
                            ->scalarNode('output')->end()
                        ->end()
                    ->end()
                ->end()                
            ->end()
        ->end();

        return $treeBuilder;
    }
}
