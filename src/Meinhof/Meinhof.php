<?php

namespace Meinhof;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Config\FileLocator;

use Meinhof\DependencyInjection\Compiler\TemplatingEnginePass;
use Meinhof\DependencyInjection\Compiler\EventListenerPass;

/**
 * Main class
 */
class Meinhof
{
    const VERSION = "0.1";

    protected $container;

    public function __construct($key)
    {
        $this->container = $this->buildContainer($key);

        // freeze the container
        $this->container->compile();

        $this->dispatchEvent('load');
    }

    protected function buildContainer($key)
    {
        // load the container
        $container = new ContainerBuilder();

        // setup basic container compiler passes
        $container->addCompilerPass(new EventListenerPass());
        $container->addCompilerPass(new TemplatingEnginePass());
       
        // set the key as a parameter
        $container->setParameter('key', $key);

        // load base services
        $configdirs = array(__DIR__.'/../../config', $key.'/config');
        $loader = new XmlFileLoader($container, new FileLocator($configdirs));
        $loader->load('services.xml');

        // load extensions
        foreach ($container->findTaggedServiceIds('extension') as $id => $attributes) {
            $extension = $container->get($id);
            if(!$extension instanceof ExtensionInterface){
                throw new \InvalidArgumentException("Invalid extension with id '${id}'.");
            }
            $container->registerExtension($extension);

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

    public function generate()
    {
        $this->dispatchEvent('generate');
    }

}