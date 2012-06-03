<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

class SiteExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function preload(ContainerBuilder $container)
    {
        // make shure this extension will be loaded
        $container->loadFromExtension($this->getAlias(), array());
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // load configuration
        $configuration = new SiteConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);

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
