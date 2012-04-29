<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;

class FilesystemExtension implements ExtensionInterface
{
	/**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new FilesystemConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs); 
    
        $base_dir = realpath($container->getParameter('key'));

        $data['paths'] = array_merge(array(
            'posts' => 'posts',
            'views' => 'views',
            'site'  => 'site'
        ), $data['paths']);
        foreach($data['paths'] as $k=>$path){
            if(substr($path,0,1) !== '/'){
                $path = $base_dir.'/'.$path;
            }
            $path = realpath($path);
            $data['paths'][$k] = $path;
        }
        $prefix = 'configuration.';
        $container->setParameter($prefix.'base_dir', $base_dir);
        $container->setParameter($prefix.'globals', $data['globals']); 
        $container->setParameter($prefix.'paths', $data['paths']); 
        foreach($data['paths'] as $name=>$path){
            $container->setParameter($prefix.'paths.'.$name, $path); 
        }
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