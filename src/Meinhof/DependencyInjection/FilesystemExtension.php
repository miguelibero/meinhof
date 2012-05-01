<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class FilesystemExtension implements ExtensionInterface
{
    protected $base_path;

    public function __construct($key, LoaderInterface $loader)
    {
        $this->base_path = realpath($key);
        // load the site configuration files
        foreach($this->getConfigurationResources($key) as $resource){
            if($loader->supports($resource)){
                $loader->load($resource);
            }
        }
    }

	/**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // load configuration
        $configuration = new FilesystemConfiguration();
        $processor = new Processor();
        $data = $processor->processConfiguration($configuration, $configs);     

        // fix configuration
        if(!isset($data['paths']) || !is_array($data['paths'])){
            $data['paths'] = array();
        }
        $data['paths'] = $this->fixConfigurationPaths($data['paths']);

        // set configuration parameters
        $prefix = 'filesystem.';
        $container->setParameter($prefix.'paths', $data['paths']); 
        foreach($data['paths'] as $name=>$path){
            $container->setParameter($prefix.'paths.'.$name, $path); 
        }
        $container->setParameter('assetic.paths.read_from', $data['paths']['public']);
        $container->setParameter('assetic.paths.write_to', $data['paths']['site']);

        // load filesystem services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $loader->load('filesystem.xml');       
    }

    protected function fixConfigurationPaths(array $paths)
    {
        $paths = array_merge(array(
            'posts'     => 'posts',
            'views'     => 'views',
            'site'      => 'site',
            'public'    => 'public',
            'base'      => $this->base_path
        ), $paths);

        foreach($paths as $k=>$path){
            if(substr($path,0,1) !== '/'){
                $path = $this->base_path.'/'.$path;
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
    	return 'filesystem';
    }

    public function getXsdValidationBasePath()
    {
    	return 'filesystem';
    }

    public function getAlias()
    {
    	return 'filesystem';
    }
}