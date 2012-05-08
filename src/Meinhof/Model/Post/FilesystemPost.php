<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Finder\Finder;

use Meinhof\Model\Site\FilesystemSite;

class FilesystemPost extends Post
{
    protected $site;

    public function __construct($slug, $key, $updated=null,
        $title=null, $view=null, array $info=array(), array $paths=array())
    {
        parent::__construct($slug, $key, $updated, $title, $view, $info);
        $this->paths = $paths;
    }

    public function setSite(FilesystemSite $site)
    {
        $this->site = $site;
    }

    protected function getSitePath($name)
    {
        return $this->site->getPath($name);
    }

    protected function getContentTemplatePath()
    {
        return $this->getSitePath('posts').'/'.$this->key;
    }

    public function getUpdated()
    {
        $updated = parent::getUpdated();
        if($updated instanceof \DateTime){
            return $updated;
        }
        $timestamp = filemtime($this->getContentTemplatePath());
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        return $date;
    }

    public function getSlug()
    {
        $slug = parent::getSlug();
        if($slug){
            return $slug;
        }
        $parts = explode('.', basename($this->key));
        return reset($parts);
    }

    protected function getContentTemplatingKey()
    {
        return $this->key;
    }

    public function getViewTemplatingKey()
    {
        $name = parent::getViewTemplatingKey();
        $base_path = $this->getSitePath('views');
        $finder = new Finder();
        $finder->files()
            ->name($name.'.*')
            ->ignoreVCS(true)
            ->in($base_path);
        foreach($finder as $file){
            return $file->getRelativePathname();
        }
        throw new \RuntimeException("Could not find template '${name}'.");
    }
}