<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Config\Loader\LoaderInterface as ConfigLoaderInterface;
use Symfony\Component\Config\Definition\Processor as ConfigProcessor;

use Meinhof\DependencyInjection\PostMatterConfiguration;

class FilesystemPostLoader extends PostLoader
{
    protected $postsPath;
    protected $viewsPath;
    protected $templating;
    protected $configLoader;

    public function __construct(EngineInterface $templating,
        $postsPath, $viewsPath, ConfigLoaderInterface $loader)
    {
        $this->postsPath = $postsPath;
        $this->viewsPath = $viewsPath;
        $this->configLoader = $loader;
        $posts = $this->loadFilesystemPosts();
        parent::__construct($posts, $templating);
    }

    protected function loadMatter($key)
    {
        if (!$this->configLoader->supports($key)) {
            return null;
        }
        $matter = $this->configLoader->load($key);
        if (!is_array($matter)) {
            return null;
        }
        $matter = array('post' => $matter);
        $processor = new ConfigProcessor();
        $configuration = new PostMatterConfiguration();

        return $processor->processConfiguration($configuration, $matter);
    }    

    public function loadFilesystemPosts()
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($this->postsPath);

        $posts = array();
        foreach ($finder as $file) {
            $path = $file->getRelativePathname();
            $config = $this->loadMatter($path);
            if(!isset($config['key'])){
                $config['key'] = $path;
            }
            $posts[] = $config;
        }

        return $posts;
    }

    protected function createPost($data)
    {
        if(is_array($data)){
            if(!isset($data['updated']) && isset($data['key'])){
                $data['updated'] = $this->getKeyUpdated($data['key']);
            }
            if(!isset($data['view'])){
                $data['view'] = 'post';
            }
            $data['view'] = $this->findView($data['view']);       
        }
        return parent::createPost($data);
    }

    protected function getKeyUpdated($key)
    {
        $path = $this->postsPath.'/'.$key;
        if(!is_readable($path)){
            return null;
        }
        $timestamp = filemtime($path);
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

    protected function findView($key)
    {
        $finder = new Finder();
        $finder->files()
            ->name($key.'.*')
            ->ignoreVCS(true)
            ->in($this->viewsPath);
        foreach ($finder as $file) {
            return $file->getRelativePathname();
        }
    }  
}