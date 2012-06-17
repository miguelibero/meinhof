<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler configures the services that have the tag `twig.extension`.
 * The tag has the aditional attribute `type`, which can be:
 * * post: the loader for the post templates, is special becaurse the posts can have a matter loader
 * * content: the loader for the content templates, normally markdown or similar
 * * view: the loader for the html views, normally twig or similar
 *
 *  Additional twig environments can be defined adding the `twig.environment` tag.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class TwigExtensionPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $tag = 'twig.environment';

        $defs = array();
        foreach ($container->findTaggedServiceIds($tag) as $id => $tags) {
            foreach ($tags as $attributes) {
                $type = $this->getTypeFromAttributes($attributes);
                $defs[$type] = $container->getDefinition($id);
            }
        }

        $method = 'addExtension';
        $prefix = 'twig.environment.';
        $tag = 'twig.extension';        

        foreach ($container->findTaggedServiceIds($tag) as $id => $tags) {
            foreach ($tags as $attributes) {
                $type = $this->getTypeFromAttributes($attributes);
                $args = array(new Reference($id));
                if (in_array($type, array_keys($defs))) {
                    $defs[$type]->addMethodCall($method, $args);
                } elseif ($type === 'all') {
                    foreach ($defs as $def) {
                        $def->addMethodCall($method, $args);
                    }
                } else {
                    throw new \InvalidArgumentException("Invalid twig environment type '${type}'.");
                }
            }
        }
    }

    /**
     * Returns the type for a templating engine tag attributes
     *
     * @param array $attrs attributes of the tag
     *
     * @return string type of the twig extension, by default 'all'
     */
    protected function getTypeFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['type'])) {
            return 'all';
        }

        return $attrs['type'];
    }
}
