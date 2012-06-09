<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Sets the localized site exporter as the main site exporter if it exists.
 * This is done this way since the localized site exporter needs the normal
 * site exporter as a parameter. Once loaded, we exchange them here.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class LocalizedSiteExporterPass implements CompilerPassInterface
{

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $id = 'translation.site_exporter';
        if ($container->has($id)) {
            $exporter = $container->get($id);
            $container->set('site_exporter', $exporter);
        }
    }
}
