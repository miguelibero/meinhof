<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
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
    public function preload(ContainerBuilder $container)
    {
        // load compilers
        $container->addCompilerPass(new AsseticFilterPass());
        $container->addCompilerPass(new AsseticResourceLoaderPass());
        $container->addCompilerPass(new AsseticFormulaLoaderPass());
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!class_exists('Assetic\\AssetManager')) {
            throw new \RuntimeException("Assetic library not loaded.");
        }

        // load assetic services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('assetic.xml');        

        // load configuration
        $configuration = new AsseticConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);

        // set configuration parameters
        $prefix = 'assetic.';
        foreach ($data as $k=>$v) {
            $container->setParameter($prefix.$k, $v);
        }

        if(isset($data['assets'])){
            $def = $container->getDefinition('action.update_assets');
            $def->addMethodCall('addAssets', array($data['assets']));
        }
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
