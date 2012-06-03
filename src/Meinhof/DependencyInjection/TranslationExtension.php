<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

use Meinhof\DependencyInjection\Compiler\TranslationLoaderPass;

class TranslationExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        if (!class_exists('Symfony\\Component\\Translation\\Translator')) {
            // do not load if library not present
            return;
        }

        $container->addCompilerPass(new TranslationLoaderPass());

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

        if ($container->hasDefinition('site_exporter')) {
            $def = $container->getDefinition('site_exporter');
            $container->setDefinition($prefix.'internal_site_exporter', $def);
        }

        // load translation services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('translation.xml');
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
