<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class MarkdownExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function preload(ContainerBuilder $container)
    {
        // load twig services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('markdown.xml');
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!class_exists('dflydev\\markdown\\MarkdownParser')) {
            throw new \RuntimeException("Markdown library not loaded.");
        }

        // load configuration
        $configuration = new TwigConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);
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
