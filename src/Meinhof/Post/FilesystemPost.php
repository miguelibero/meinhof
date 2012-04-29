<?php

namespace Meinhof\Post;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Definition\Processor;

use Meinhof\Post\PostConfiguration;

class FilesystemPost implements PostInterface
{
    protected $key;
    protected $title;
    protected $view;
    protected $updated = null;
    protected $info = array();

    protected $paths = array();
    protected $loader;

    public function __construct($key, $title=null, $updated=null, $view=null,
        array $info=array(), array $paths=array())
    {
        $this->key = $key;
        $this->title = $title;
        $this->view = $view;
        if($this->updated){
            $this->setUpdated($updated);
        }
        $this->info = $info;
        $this->paths = $paths;
    }

    protected function setUpdated($updated)
    {
        if(is_int($updated) || is_numeric($updated)){
            $this->updated = new \DateTime();
            $this->updated->setTimestamp($updated);
        }else if(is_string($updated)){
            $this->updated = new \DateTime($updated);
        }else if($updated instanceof \DateTime){
            $this->updated = $updated;
        }else{
            throw new \InvalidArgumentException("Could not set updated time.");
        }
    }

    protected function getPath($name)
    {
        if(!is_array($this->paths) || !isset($this->paths[$name])){
            throw new \InvalidArgumentException("Could not find path ${name}.");
        }
        $path = $this->paths[$name];
        if(substr($path,0,1) !== '/'){
            if(!isset($this->paths['base'])){
                throw new \InvalidArgumentException('No base path defined.');
            }
            $path = $this->paths['base'].'/'.$path;
        }
        return $path;
    }

    protected function getContentTemplatePath()
    {
        return $this->getPath('posts').'/'.$this->key;
    }

    public function getTitle()
    {
        $title = $this->getSlug();
        $title = str_replace('-', ' ', $title);
        $title = ucwords($title);
        return $title;
    }

    public function getUpdated()
    {
        if($this->updated instanceof \DateTime){
            return $this->updated;
        }
        $timestamp = filemtime($this->getContentTemplatePath());
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $data;
    }

    public function getSlug()
    {
        $parts = explode('.', $this->key);
        return reset($parts);
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getContentTemplatingKey()
    {
        return $this->key;
    }

    public function getViewName()
    {
        if($this->view){
            return $this->view;
        }else{
            return 'post';
        }
    }

    public function getViewTemplatingKey()
    {
        $base_path = $this->getPath('views');
        $finder = new Finder();
        $finder->files()
            ->name($this->getViewName().'.*')
            ->ignoreVCS(true)
            ->in($base_path);
        foreach($finder as $file){
            $path = $file->getRealPath();
            if(substr($path, 0, strlen($base_path)) === $base_path){
                $path = substr($path, strlen($base_path));
                $path = trim($path,'/');
            }
            return $path;
        }
    }

    public static function fromArray(array $config, LoaderInterface $loader=null)
    {
        if(!isset($config['key'])){
            throw new \InvalidArgumentException('No key supplied.');
        }

        if($loader){
            $matter = self::loadMatter($config['key'], $loader);
            $config = array_merge($matter, $config);
        }

        if(!isset($config['info']) || !is_array($config['info'])){
            $config['info'] = array();
        }
        if(!isset($config['paths']) || !is_array($config['paths'])){
            $config['paths'] = array();
        }        
        $config = array_merge(array(
            'title'     => null,
            'updated'   => null,
            'view'      => null
        ), $config);

        return new self($config['key'], $config['title'],
            $config['updated'], $config['view'],
            $config['info'], $config['paths']);
    }

    protected static function loadMatter($key, LoaderInterface $loader)
    {
        if(!$loader->supports($key)){
            return null;
        }
        $matter = $loader->load($key);
        if(!is_array($matter)){
            return null;
        }
        $matter = array('meinhof_post'=>$matter);
        $processor = new Processor();
        $configuration = new PostMatterConfiguration();
        return $processor->processConfiguration($configuration, $matter);  
    }
}