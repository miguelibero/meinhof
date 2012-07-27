<?php

namespace Meinhof\Model\Category;

use Symfony\Component\Finder\Finder;

use Meinhof\Model\Site\FilesystemSite;
use Meinhof\Model\Post\PostInterface;

class FilesystemCategory extends AbstractCategory
{
    protected $site;

    public function setSite(FilesystemSite $site)
    {
        $this->site = $site;
    }

    protected function getSitePath($name)
    {
        if(!$this->site){
            throw new \RuntimeException("No site configured");
        }
        return $this->site->getPath($name);
    }

    public function getPosts()
    {
        $posts = array();
        if($this->site){
            foreach($this->site->getPosts() as $post){
                if(!$post instanceof PostInterface){
                    continue;
                }
                foreach($post->getCategories() as $cat){
                    if($cat->getKey() === $this->getKey()){
                        $posts[] = $post;
                        break;
                    }
                }
            }
        }
        return $posts;
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
        foreach ($finder as $file) {
            return $file->getRelativePathname();
        }
        throw new \RuntimeException("Could not find template '${name}'.");
    }


    public static function fromArray(array $config)
    {
        $config = array_merge(array(
            'key'   => null,
            'name'  => null,
            'slug'  => null
        ), $config);

        return new static($config['key'], $config['name'], $config['slug']);
    }    
}
