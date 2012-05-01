<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AsseticFormulaLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if(!$container->hasDefinition('assetic.formula_loader_manager')){
            return;
        }
        $def = $container->getDefinition('assetic.formula_loader_manager');
        foreach ($container->findTaggedServiceIds('assetic.formula_loader') as $id => $attributes) {
            $type = $this->getTypeFromAttributes($attributes);
            $def->addMethodCall('setLoader', array($type, new Reference($id)));
        }
    }

    protected function getTypeFromAttributes($attrs)
    {
        if(!is_array($attrs) || !isset($attrs[0]) || !is_array($attrs[0]) || !isset($attrs[0]['type'])){
            throw new \InvalidArgumentException("Assetic formula loader without a type.");
        }
        return $attrs[0]['type'];
    }
}