<?php

namespace Meinhof;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Meinhof\DependencyInjection\ExtensionInterface;

/**
 * Main class
 *
 * Implements the generation of an html site
 */
class Meinhof
{
    const VERSION = "0.1";

    protected $container;

    public function __construct($key)
    {
        // load the container
        $this->container = new ContainerBuilder();
        $loader = new XmlFileLoader($this->container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.xml');

        // set the key as a parameter
        $this->container->setParameter('key', $key);

        // register the extension
        $extension = $this->container->get('extension');
        if(!$extension instanceof ExtensionInterface){
            throw new \InvalidArgumentException("Invalid extension.");
        }
        $this->container->registerExtension($extension);

        // load the configuration resources
        $resources = $extension->getConfigurationResources($key);
        $loader = $this->container->get('config_loader');
        foreach($resources as $resource){
            if($loader->supports($resource)){
                $loader->load($resource);
            }
        }

        // load the extension configuration
        $configs = $this->container->getExtensionConfig($extension->getAlias());
        $extension->load($configs, $this->container);

        // freeze the container
        $this->container->compile();
    }

    public function generate()
    {
        $this->generatePosts();
        $this->dumpAssets();
    }

    public function generatePosts()
    {
        $config = $this->container->get('configuration');
        $posts = $config->getPosts();
        
        $templating = $this->container->get('templating');

        foreach($posts as $post){
            if(!$templating->exists($post)){
                throw new \InvalidArgumentException("Post '${post}' does not exist.");
            }
            if(!$templating->supports($post)){
                throw new \InvalidArgumentException("Post '${post}' does not have a valid format.");   
            }
            $params = $config->getGlobals();
            $content = $templating->render($post, $params);

            $params['content'] = $params;
            $layout = $config->getLayoutForPost($post);
            $content = $templating->render($layout, $params);

            $config->savePost($post, $content);
        }        
    }

    public function dumpAssets()
    {
        $writer = $this->container->get('assetic.asset_writer');
        $mng = $this->container->get('assetic.asset_manager');
        $writer->writeManagerAssets($mng);
    }
}