<?php

namespace Meinhof;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

use Assetic\Factory\Resource\ResourceInterface as AsseticResourceInterface;
use Assetic\Factory\LazyAssetManager;

use Meinhof\DependencyInjection\ExtensionInterface;
use Meinhof\Post\PostInterface;

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
        $site = $this->container->get('site');
        $tplpost = $this->container->get('templating.post');
        $tplview = $this->container->get('templating.view');

        foreach($site->getPosts() as $post){
            if(!$post instanceof PostInterface){
                continue;
            }
            $ckey = $post->getContentTemplatingKey();
            if(!$tplpost->exists($ckey)){
                throw new \InvalidArgumentException("Post template '${post}' does not exist.");
            }
            if(!$tplpost->supports($ckey)){
                throw new \InvalidArgumentException("Post template '${post}' does not have a valid format.");   
            }
            $params = $site->getGlobals();
            $params = array_merge($params, $post->getGlobals());
            $content = $tplpost->render($ckey, $params);

            $params['content'] = $params;
            $params['post'] = $post;
            $vkey = $post->getViewTemplatingKey();
            if($vkey){
                if(!$tplview->exists($vkey)){
                    throw new \InvalidArgumentException("View template '${vkey}' does not exist.");
                }
                if(!$tplview->supports($vkey)){
                    throw new \InvalidArgumentException("View template '${vkey}' does not have a valid format.");
                }            
                $content = $tplview->render($vkey, $params);
            }

            $site->savePost($post, $content);
        }        
    }

    public function dumpAssets()
    {
        $site = $this->container->get('site');
        $manager = $this->container->get('assetic.asset_manager');
        if(!$manager instanceof LazyAssetManager){
            throw new \InvalidArgumentException("Need a lazy asset manager to dump the assets.");
        }
        $loader = $this->container->get('assetic.resource_loader');
        foreach($site->getTemplates() as $tpl){
            $resource = $loader->getResource($tpl);
            $type = $loader->getResourceType($tpl);
            if($resource instanceof AsseticResourceInterface){
                $manager->addResource($resource, $type);
            }
        }
        $writer = $this->container->get('assetic.asset_writer');
        $writer->writeManagerAssets($manager);
    }
}