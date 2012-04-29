<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class FilesystemExtension implements ExtensionInterface
{
	/**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // load configuration
        $configuration = new FilesystemConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);     
        $base_path = realpath($container->getParameter('key'));

        // fix configuration
        if(!isset($data['paths']) || !is_array($data['paths'])){
            $data['paths'] = array();
        }
        $data['paths'] = $this->fixConfigurationPaths($data['paths'], $base_path);

        // set configuration parameters
        $prefix = 'site.';
        $container->setParameter($prefix.'globals', $data['globals']); 
        $container->setParameter($prefix.'paths', $data['paths']); 
        foreach($data['paths'] as $name=>$path){
            $container->setParameter($prefix.'paths.'.$name, $path); 
        }

        // load filesystem services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $loader->load('filesystem.xml');        
    }

    protected function fixConfigurationPaths(array $paths, $base)
    {
        $paths = array_merge(array(
            'posts'     => 'posts',
            'views'     => 'views',
            'site'      => 'site',
            'public'    => 'public',
            'base'      => $base
        ), $paths);

        foreach($paths as $k=>$path){
            if(substr($path,0,1) !== '/'){
                $path = $base.'/'.$path;
            }
            $paths[$k] = realpath($path);
        }
        return $paths;
    }

    public function getConfigurationResources($key)
    {
        // find the configuration files
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($key.'/config');

        $resources = array();

        foreach($finder as $file){
            $resources[] = $file->getRealPath();
        }
        return $resources;
    }


    public function getNamespace()
    {
    	return 'meinhof';
    }

    public function getXsdValidationBasePath()
    {
    	return 'meinhof';
    }

    public function getAlias()
    {
    	return 'meinhof';
    }
}