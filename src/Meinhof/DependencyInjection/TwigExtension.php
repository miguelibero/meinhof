<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

use Meinhof\DependencyInjection\Compiler\TwigExtensionPass;

class TwigExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!class_exists('Twig_Environment')) {
            // do not load if library not present
            return;
        }

        // load compiler
        $container->addCompilerPass(new TwigExtensionPass());

        // load configuration
        $configuration = new TwigConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);

        // load twig services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('twig.xml');
    }

    public function getNamespace()
    {
        return 'twig';
    }

    public function getXsdValidationBasePath()
    {
        return 'twig';
    }

    public function getAlias()
    {
        return 'twig';
    }
}
