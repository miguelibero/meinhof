<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Definition\Processor;

use Meinhof\Post\PostConfiguration;

class Post extends AbstractPost
{
    protected $slug;
    protected $key;
    protected $title;
    protected $view;
    protected $updated = null;
    protected $info = array();

    public function __construct($slug, $key, $updated=null,
        $title=null, $view=null, array $info=array())
    {
        $this->slug = $slug;
        $this->key = $key;
        $this->title = $title;
        $this->view = $view;
        if($this->updated !== null){
            $this->setUpdated($updated);
        }
        $this->info = $info;
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

    public function getTitle()
    {
        if($this->title){
            return $this->title;
        }
        return parent::getTitle();
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getInfo()
    {
        return $this->info;
    }

    protected function getContentTemplatingKey()
    {
        return $this->key;
    }

    public function getViewTemplatingKey()
    {
        if($this->view){
            return $this->view;
        }
        return parent::getViewTemplatingKey();
    }

    public static function fromArray(array $config, LoaderInterface $loader=null)
    {
        if($loader && isset($config['key'])){
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
            'key'       => null,
            'slug'      => null,
            'title'     => null,
            'updated'   => null,
            'view'      => null
        ), $config);

        return new static($config['slug'], $config['key'],
            $config['updated'], $config['title'], $config['view'],
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
        $matter = array('post' => $matter);
        $processor = new Processor();
        $configuration = new PostMatterConfiguration();
        return $processor->processConfiguration($configuration, $matter);  
    }
}