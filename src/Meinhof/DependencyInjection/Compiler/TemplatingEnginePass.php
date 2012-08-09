<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler configures the services that have the tag `templating.engine`.
 * The tag has the aditional attribute `type`, which can be:
 * * post: the loader for the post templates, is special becaurse the posts can have a matter loader
 * * content: the loader for the content templates, normally markdown or similar
 * * view: the loader for the html views, normally twig or similar
 *
 * Additional templating types can be defined adding the `templating` tag.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class TemplatingEnginePass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $tag = 'templating';

        // load templating environments
        $defs = array();
        foreach ($container->findTaggedServiceIds($tag) as $id=>$tags) {
            foreach ($tags as $attributes) {
                $type = $this->getTypeFromAttributes($attributes);
                $defs[$type] = $container->getDefinition($id);
            }
        }

        $method = 'addEngine';
        $prefix = 'templating.';
        $tag = 'templating.engine';

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
                    throw new \InvalidArgumentException("Invalid templating environment type '${type}'.");
                }
            }
        }
    }

    /**
     * Returns the type for a templating engine tag attributes
     *
     * @param array $attrs attributes of the tag
     *
     * @return string type of the templating engine, by default 'all'
     */
    protected function getTypeFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['type'])) {
            return 'all';
        }

        return $attrs['type'];
    }
}
