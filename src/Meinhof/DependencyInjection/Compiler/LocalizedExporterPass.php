<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Sets the localized exporter as the main exporter if it exists.
 * This is done this way since the localized exporter needs the normal
 * exporter as a parameter. Once loaded, we exchange them here.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class LocalizedExporterPass implements CompilerPassInterface
{

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $id = 'translation.exporter';
        if ($container->has($id)) {
            $exporter = $container->get($id);
            $container->set('exporter', $exporter);
        }
    }
}
