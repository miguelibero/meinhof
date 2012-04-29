<?php

namespace Meinhof\Post;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class PostMatterConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('meinhof_post');

        $rootNode
            ->children()
                ->scalarNode('title')->end()
                ->scalarNode('view')->end()
                ->variableNode('info')->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}