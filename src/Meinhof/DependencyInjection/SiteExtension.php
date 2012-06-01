<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;

class SiteExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // load configuration
        $configuration = new SiteConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);

        if (!isset($data['urls']) || !is_array($data['urls'])) {
            $data['urls'] = array();
        }

        // set configuration parameters
        $prefix = 'site.';
        foreach ($data as $k=>$v) {
            $container->setParameter($prefix.$k, $v);
        }
    }

    public function getNamespace()
    {
        return 'site';
    }

    public function getXsdValidationBasePath()
    {
        return 'site';
    }

    public function getAlias()
    {
        return 'site';
    }
}
