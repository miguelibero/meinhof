<?php

namespace Meinhof;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

use Assetic\Factory\Resource\ResourceInterface as AsseticResourceInterface;
use Assetic\Factory\LazyAssetManager as AsseticLazyAssetManager;
use Assetic\Factory\Loader\FormulaLoaderInterface as AsseticFormulaLoaderInterface;

use Meinhof\DependencyInjection\Compiler\TemplatingEnginePass;
use Meinhof\DependencyInjection\Compiler\TwigExtensionPass;
use Meinhof\DependencyInjection\Compiler\AsseticFilterPass;
use Meinhof\DependencyInjection\Compiler\AsseticFormulaLoaderPass;
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

        // setup container compiler passes
        $this->container->addCompilerPass(new TemplatingEnginePass());
        $this->container->addCompilerPass(new TwigExtensionPass());
        $this->container->addCompilerPass(new AsseticFilterPass());
        $this->container->addCompilerPass(new AsseticFormulaLoaderPass());

        $loader = new XmlFileLoader($this->container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.xml');
        $loader->load('templating.xml');
        $loader->load('assetic.xml');

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
        $this->generatePages();
        $this->dumpAssets();
    }

    public function generatePages()
    {
        $site = $this->container->get('site');
        $posts = $site->getPosts();
        $tplview = $this->container->get('templating.view');
    }

    public function generatePosts()
    {
        $site = $this->container->get('site');
        $posts = $site->getPosts();
        $tplpost = $this->container->get('templating.post');
        $tplview = $this->container->get('templating.view');

        $globals = $site->getGlobals();
        $globals['posts'] = $posts;

        foreach($posts as $post){
            if(!$post instanceof PostInterface){
                continue;
            }
            $ckey = $post->getContentTemplatingKey();
            if(!$tplpost->exists($ckey)){
                throw new \InvalidArgumentException("Post template '${ckey}' does not exist.");
            }
            if(!$tplpost->supports($ckey)){
                throw new \InvalidArgumentException("Post template '${ckey}' does not have a valid format.");   
            }
            $params = $globals;
            $params['post'] = $post;
            $content = $tplpost->render($ckey, $params);

            $params['content'] = $content;
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
        if(!$manager instanceof AsseticLazyAssetManager){
            throw new \InvalidArgumentException("Need a lazy asset manager to dump the assets.");
        }
        // load formula loaders, done lazily to avoid circular dependencies
        $loaders = $this->container->getParameter('assetic.formula_loaders');
        foreach($loaders as $alias=>$id){
            $loader = $this->container->get($id);
            if(!$loader instanceof AsseticFormulaLoaderInterface){
                throw new \InvalidArgumentException("Invalid assetic formula loader '${alias}'.");
            }
            $manager->setLoader($alias, $loader);
        }
        // load template resources
        $loader = $this->container->get('assetic.resource_loader');
        foreach($site->getViews() as $view){
            $resource = $loader->getResource($view);
            $type = $loader->getResourceType($view);
            if(!$resource instanceof AsseticResourceInterface){
                throw new \InvalidArgumentException("Invalid view resource '${view}'.");
            }
            $manager->addResource($resource, $type);
        }
        $writer = $this->container->get('assetic.asset_writer');
        $writer->writeManagerAssets($manager);
    }
}