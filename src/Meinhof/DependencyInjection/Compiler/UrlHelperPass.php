<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add new url helpers using the `url_helper`tag.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class UrlHelperPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $defid = 'url_helper';
        if (!$container->hasDefinition($defid)) {
            return;
        }
        $def = $container->getDefinition($defid);
        foreach ($container->findTaggedServiceIds('url_helper') as $id => $tags) {
            foreach ($tags as $attributes) {
                $class = $this->getClassFromAttributes($attributes);
                $def->addMethodCall('addHelper', array($class, new Reference($id)));
            }
        }
    }

    /**
     * Returns the url helper class from the tag attributes.
     *
     * @param mixed $attrs tag attributes
     *
     * @return string class
     *
     * @throws \InvalidArgumentException if the attributes are invalid
     */
    protected function getClassFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['class'])) {
            throw new \InvalidArgumentException("Url helper without a class.");
        }

        return $attrs['class'];
    }
}
