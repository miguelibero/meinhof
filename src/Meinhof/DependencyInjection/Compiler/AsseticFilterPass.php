<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add new assetic filters using the `assetic.filter`tag.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class AsseticFilterPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
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

    /**
     * Returns the assetic filter alias from the tag attributes.
     *
     * @param mixed $attrs tag attributes
     *
     * @return string alias
     *
     * @throws \InvalidArgumentException if the attributes are invalid
     */
    protected function getAliasFromAttributes($attrs)
    {
        if (!is_array($attrs) || !isset($attrs['alias'])) {
            throw new \InvalidArgumentException("Assetic filter without an alias.");
        }

        return $attrs['alias'];
    }
}
