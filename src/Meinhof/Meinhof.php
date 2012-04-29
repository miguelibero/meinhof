<?php

namespace Meinhof;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

use Meinhof\DependencyInjection\SiteExtension;

class Meinhof
{
	const VERSION = "0.1";

	protected $container;
    protected $extension;

	public function __construct($dir)
	{
        // load the container
		$this->container = new ContainerBuilder();
		$loader = new XmlFileLoader($this->container, new FileLocator(__DIR__.'/../../config'));
		$loader->load('services.xml');

        // register the extension
        $this->extension = $this->container->get('extension');
        $this->container->registerExtension($this->extension);        
        
        // find the configuration files
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($dir.'/config');

        // load the configuration files
        $loader = $this->container->get('config_loader');
        foreach($finder as $file){
            $path = $file->getRealPath();
            if($loader->supports($path)){
                $loader->load($path);
            }
        }

        // load the extension configuration
        $configs = $this->container->getExtensionConfig($this->extension->getAlias());
        $this->extension->load($configs, $this->container);

        // freeze the container
        $this->container->compile();
	}

	public function generate()
	{

	}
}