<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class TranslationConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('translation');

        $rootNode
            ->children()
                ->scalarNode('default_locale')->end()
                ->arrayNode('locales')
                    ->prototype('scalar')->end()
                ->end();

        return $treeBuilder;
    }
}