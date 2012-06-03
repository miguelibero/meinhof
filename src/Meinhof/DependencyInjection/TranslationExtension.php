<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

use Meinhof\DependencyInjection\Compiler\TranslationLoaderPass;
use Meinhof\DependencyInjection\Compiler\LocalizedSiteExporterPass;

class TranslationExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function preload(ContainerBuilder $container)
    {
        // load compilers
        $container->addCompilerPass(new TranslationLoaderPass());
        $container->addCompilerPass(new LocalizedSiteExporterPass());

        // load translation services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('translation.xml');
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!class_exists('Symfony\\Component\\Translation\\Translator')) {
            throw new \RuntimeException("Symfony translator component not loaded.");
        }

        // load configuration
        $configuration = new TranslationConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);

        if (!isset($data['default_locale']) || !$data['default_locale']) {
            $data['default_locale'] = 'C';
        }
        if (!isset($data['locales']) || count($data['locales']) === 0) {
            $data['locales'] = array('C');
        }

        $prefix = 'translation.';
        $container->setParameter($prefix.'default_locale', $data['default_locale']);
        $container->setParameter($prefix.'locales', $data['locales']);
    }

    public function getNamespace()
    {
        return 'translation';
    }

    public function getXsdValidationBasePath()
    {
        return 'translation';
    }

    public function getAlias()
    {
        return 'translation';
    }
}
