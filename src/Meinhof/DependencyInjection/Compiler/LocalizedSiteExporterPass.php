<?php

namespace Meinhof\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class LocalizedSiteExporterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $id = 'translation.site_exporter';
        if ($container->has($id)) {
            $exporter = $container->get($id);
            $container->set('site_exporter', $exporter);
        }
    }
}
