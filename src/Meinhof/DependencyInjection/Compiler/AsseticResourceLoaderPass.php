<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AsseticResourceLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $def = $container->getDefinition('assetic.resource_loader');
        foreach ($container->findTaggedServiceIds('assetic.resource_loader') as $id => $attributes) {
            $type = $this->getTypeFromAttributes($attributes);
            $def->addMethodCall('setLoader', array($type, new Reference($id)));
        }
    }

    protected function getTypeFromAttributes($attrs)
    {
        if(!is_array($attrs) || !isset($attrs[0]) || !is_array($attrs[0]) || !isset($attrs[0]['type'])){
            throw new \InvalidArgumentException("Assetic resource loader without a type.");
        }
        return $attrs[0]['type'];
    }
}