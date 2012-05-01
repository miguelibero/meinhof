<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AsseticFilterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if(!$container->hasDefinition('assetic.filter_manager')){
            return;
        }
        $def = $container->getDefinition('assetic.filter_manager');
        foreach ($container->findTaggedServiceIds('assetic.filter') as $id => $attributes) {
            $alias = $this->getAliasFromAttributes($attributes);
            $def->addMethodCall('addFilter', array(new Reference($id), $alias));
        }
    }

    protected function getAliasFromAttributes($attrs)
    {
        if(!is_array($attrs) || !isset($attrs[0]) || !is_array($attrs[0]) || !isset($attrs[0]['alias'])){
            throw new \InvalidArgumentException("Assetic filter without an alias.");
        }
        return $attrs[0]['alias'];
    }
}