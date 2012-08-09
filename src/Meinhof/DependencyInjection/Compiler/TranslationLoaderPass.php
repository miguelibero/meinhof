<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler configures the services that have the tag `translation.loader`.
 * The services should implement TranslationLoaderInterface and only will be loaded
 * if the translation extension is loaded.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class TranslationLoaderPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
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

    /**
     * Returns the format for the translation loader
     *
     * @param array $attrs attributes of the tag
     *
     * @return string format of the loader
     */
    protected function getFormatFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['format'])) {
            throw new \InvalidArgumentException("Translation loader without a format.");
        }

        return $attrs['format'];
    }
}
