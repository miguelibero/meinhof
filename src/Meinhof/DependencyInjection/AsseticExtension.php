<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

use Meinhof\DependencyInjection\Compiler\AsseticFilterPass;
use Meinhof\DependencyInjection\Compiler\AsseticFormulaLoaderPass;
use Meinhof\DependencyInjection\Compiler\AsseticResourceLoaderPass;

class AsseticExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!class_exists('Assetic\\AssetManager')) {
            // do not load if library not present
            return;
        }

        // load compilers
        $container->addCompilerPass(new AsseticFilterPass());
        $container->addCompilerPass(new AsseticResourceLoaderPass());
        $container->addCompilerPass(new AsseticFormulaLoaderPass());

        // load configuration
        $configuration = new AsseticConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);

        // load assetic services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('assetic.xml');
    }

    public function getNamespace()
    {
        return 'assetic';
    }

    public function getXsdValidationBasePath()
    {
        return 'assetic';
    }

    public function getAlias()
    {
        return 'assetic';
    }
}
