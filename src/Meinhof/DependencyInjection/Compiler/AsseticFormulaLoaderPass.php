<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AsseticFormulaLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $defid = 'assetic.formula_loader_manager';
        if (!$container->hasDefinition($defid)) {
            return;
        }
        $def = $container->getDefinition($defid);
        foreach ($container->findTaggedServiceIds('assetic.formula_loader') as $id => $tags) {
            foreach ($tags as $attributes) {
                $type = $this->getTypeFromAttributes($attributes);
                $def->addMethodCall('setLoader', array($type, new Reference($id)));
            }
        }
    }

    protected function getTypeFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['type'])) {
            throw new \InvalidArgumentException("Assetic formula loader without a type.");
        }

        return $attrs['type'];
    }
}
