<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add new site models using the `model_loader` tag.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class ModelLoaderPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $defid = 'site';
        if (!$container->hasDefinition($defid)) {
            return;
        }
        $def = $container->getDefinition($defid);
        foreach ($container->findTaggedServiceIds('model.loader') as $id => $tags) {
            foreach ($tags as $attributes) {
                $def->addMethodCall('addModelLoader', array(new Reference($id)));
            }
        }
    }
}
