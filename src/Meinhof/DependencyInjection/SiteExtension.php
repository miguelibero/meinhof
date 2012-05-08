<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

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

        if(!isset($data['urls']) || !is_array($data['urls'])){
            $data['urls'] = array();
        }

        // set configuration parameters
        $prefix = 'site.';
        $container->setParameter($prefix.'globals', $data['globals']); 
        $container->setParameter($prefix.'urls', $data['urls']); 
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