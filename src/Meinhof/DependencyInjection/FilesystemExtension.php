<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class FilesystemExtension implements PreloadingExtensionInterface
{
    protected $base_path;
    protected $loader;

    public function __construct($dir, LoaderInterface $loader)
    {
        $this->base_path = $dir;
        $this->loader = $loader;
    }

    public function preload()
    {
        // load the site configuration files
        $resources = $this->getConfigurationResources($this->base_path);
        foreach ($resources as $resource) {
            if ($this->loader->supports($resource)) {
                $this->loader->load($resource);
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
        if (!isset($data['paths']) || !is_array($data['paths'])) {
            $data['paths'] = array();
        }
        $data['paths'] = $this->fixConfigurationPaths($data['paths']);

        // set configuration parameters
        $prefix = 'filesystem.';
        foreach ($data as $k=>$v) {
            $container->setParameter($prefix.$k, $v);
        }
        foreach ($data['paths'] as $name=>$path) {
            $container->setParameter($prefix.'paths.'.$name, $path);
        }

        // load filesystem services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('filesystem.xml');

        if(!$container->hasDefinition('translator')){
            $container->removeDefinition('filesystem.action.load_translation_resources');
        }
    }

    protected function fixConfigurationPaths(array $paths)
    {
        $paths = array_merge(array(
            'posts'         => 'posts',
            'views'         => 'views',
            'web'           => 'web',
            'public'        => 'public',
            'content'       => 'content',
            'config'        => 'config',
            'translations'  => 'translations',
            'base'          => $this->base_path
        ), $paths);

        foreach ($paths as $k=>$path) {
            if ($path === '.' || substr($path,0,2) !== './') {
                $path = getcwd().'/'.$path;
            }
            $paths[$k] = $path;
        }
        foreach ($paths as $k=>$path) {
            if (substr($path,0,1) !== '/') {
                $path = $this->base_path.'/'.$path;
            }
            $paths[$k] = $path;
        }

        return $paths;
    }

    public function getConfigurationResources($dir)
    {
        $resources = array();
        $dir = $dir.'/config';

        if (!is_readable($dir) || !is_dir($dir)) {
            return $resources;
        }

        // find the configuration files
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($dir);

        foreach ($finder as $file) {
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
