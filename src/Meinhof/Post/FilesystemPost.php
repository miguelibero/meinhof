<?php

namespace Meinhof\Post;

use Symfony\Component\Finder\Finder;

class FilesystemPost implements PostInterface
{
    protected $key;
    protected $posts_path;
    protected $views_path;
    protected $globals = array();

    public function __construct($key, $posts_path, $views_path)
    {
        $this->key = $key;
        $this->posts_path = $posts_path;
        $this->views_path = $views_path;
    }

    protected function getPath()
    {
        $path = $this->key;
        if(substr($path,0,1) !== '/'){
            $path = $this->posts_path.'/'.$path;
        }
        return $path;
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
        $timestamp = filemtime($this->getPath());
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $data;
    }

    public function getSlug()
    {
        $parts = explode('.', $this->key);
        return reset($parts);
    }

    public function getGlobals()
    {
        return $this->globals;
    }

    public function getContentTemplatingKey()
    {
        return $this->key;
    }

    public function getLayout()
    {
        return 'post';
    }

    public function getViewTemplatingKey()
    {
        $finder = new Finder();
        $finder->files()
            ->name($this->getLayout().'.*')
            ->ignoreVCS(true)
            ->in($this->views_path);
        foreach($finder as $file){
            $path = $file->getRealPath();
            if(substr($path, 0, strlen($this->views_path)) === $this->views_path){
                $path = substr($path, strlen($this->views_path));
                $path = trim($path,'/');
            }
            return $path;
        }
    }
}