<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AsseticFilterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $defid = 'assetic.filter_manager';
        if (!$container->hasDefinition($defid)) {
            return;
        }
        $def = $container->getDefinition($defid);
        foreach ($container->findTaggedServiceIds('assetic.filter') as $id => $tags) {
            foreach ($tags as $attributes) {
                $alias = $this->getAliasFromAttributes($attributes);
                $def->addMethodCall('addFilter', array(new Reference($id), $alias));
            }
        }
    }

    protected function getAliasFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['alias'])) {
            throw new \InvalidArgumentException("Assetic filter without an alias.");
        }

        return $attrs['alias'];
    }
}
