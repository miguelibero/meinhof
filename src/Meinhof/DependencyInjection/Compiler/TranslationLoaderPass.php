<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TranslationLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $method = 'addLoader';
        $tag = 'translation.loader';
        $id = 'translator';

        if (!$container->hasDefinition($id)) {
            return;
        }

        $def = $container->getDefinition($id);

        foreach ($container->findTaggedServiceIds($tag) as $id => $tags) {
            foreach ($tags as $attributes) {
                $format = $this->getFormatFromAttributes($attributes);
                $args = array($format, new Reference($id));
                $def->addMethodCall($method, $args);
            }
        }
    }

    protected function getFormatFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['format'])) {
            throw new \InvalidArgumentException("Translation loader without a format.");
        }

        return $attrs['format'];
    }
}
