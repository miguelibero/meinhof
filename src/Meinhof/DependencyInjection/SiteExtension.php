<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Processor;

class SiteExtension implements ExtensionInterface
{
    protected $template_globals;

	/**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new SiteConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs); 

        $this->template_globals = $data['globals'];
    }

    function getTemplateGlobals()
    {
        return $this->template_globals;
    }

    function getNamespace()
    {
    	return 'meinhof';
    }

    function getXsdValidationBasePath()
    {
    	return 'meinhof';
    }

    function getAlias()
    {
    	return 'meinhof';
    }
}