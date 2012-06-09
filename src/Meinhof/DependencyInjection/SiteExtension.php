<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class SiteExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function preload(ContainerBuilder $container)
    {
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

        // load site services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('site.xml');        
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
