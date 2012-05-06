<?php

namespace Meinhof;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Config\FileLocator;

use Meinhof\DependencyInjection\PreloadingExtensionInterface;
use Meinhof\DependencyInjection\Compiler\TemplatingEnginePass;
use Meinhof\DependencyInjection\Compiler\EventListenerPass;

/**
 * Main class
 */
class Meinhof
{
    const VERSION = "0.1";

    protected $container;

    public function __construct($dir, InputInterface $input = null, OutputInterface $output = null)
    {
        // load libraries defined in site configuration
        $autoload = realpath($dir.'/vendor/autoload.php');
        if(is_readable($autoload)){
            require_once($autoload);
        }

        $this->container = $this->buildContainer($dir);

        // load input and output
        $this->container->set('input', $input);
        $this->container->set('output', $output);

        // freeze the container
        $this->container->compile();

        $this->dispatchEvent('load');
    }

    protected function buildContainer($dir)
    {
        // load the container
        $container = new ContainerBuilder();

        // setup basic container compiler passes
        $container->addCompilerPass(new EventListenerPass());
        $container->addCompilerPass(new TemplatingEnginePass());
       
        // set the key as a parameter
        $container->setParameter('base_dir', $dir);

        // load base services
        $configdirs = array(__DIR__.'/Resources/config', $dir, $dir.'/config');
        $loader = new XmlFileLoader($container, new FileLocator($configdirs));
        $loader->load('services.xml');

        // load extensions
        foreach ($container->findTaggedServiceIds('extension') as $id => $attributes) {
            $extension = $container->get($id);
            if(!$extension instanceof ExtensionInterface){
                throw new \InvalidArgumentException("Invalid extension with id '${id}'.");
            }
            $container->registerExtension($extension);

            // preload the extension
            if($extension instanceof PreloadingExtensionInterface){
                $extension->preload();
            }

            // load the extension configuration
            $configs = $container->getExtensionConfig($extension->getAlias());
            $extension->load($configs, $container);
        }

        return $container;
    }

    protected function dispatchEvent($event)
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($event);
    }

    public function init()
    {
        $this->dispatchEvent('init');
    }    

    public function update()
    {
        $dir = $this->container->getParameter('base_dir');
        if(!is_dir($dir) || !is_readable($dir)){
            throw new \InvalidArgumentException("'${dir}' is not a valid readable directory.");
        }
        $this->dispatchEvent('update');
    }

}