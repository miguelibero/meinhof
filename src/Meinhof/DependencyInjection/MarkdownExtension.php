<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class MarkdownExtension implements ExtensionInterface
{
	/**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // load configuration
        $configuration = new TwigConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);     

        // load twig services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $loader->load('markdown.xml');        
    }

    public function getNamespace()
    {
    	return 'markdown';
    }

    public function getXsdValidationBasePath()
    {
    	return 'markdown';
    }

    public function getAlias()
    {
    	return 'markdown';
    }
}