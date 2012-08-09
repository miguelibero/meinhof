<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Finder\Finder;

use Meinhof\Model\Site\FilesystemSite;
use Meinhof\Model\Category\FilesystemCategory;

class FilesystemPost extends Post
{
    protected $site;

    public function setSite(FilesystemSite $site)
    {
        $this->site = $site;
        foreach($this->getCategories() as $category) {
            if($category instanceof FilesystemCategory){
                $category->setSite($site);
            }
        }
    }

    protected function getSitePath($name)
    {
        return $this->site->getPath($name);
    }

    protected function getContentTemplatePath()
    {
        return $this->getSitePath('posts').'/'.$this->key;
    }

    protected function createCategory($category)
    {
        if (is_string($category)) {
            $category = new FilesystemCategory($category);
        }
        if (is_array($category)) {
            $category = FilesystemCategory::fromArray($category);
        }
        return $category;
    }    

    public function getUpdated()
    {
        $updated = parent::getUpdated();
        if ($updated instanceof \DateTime) {
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
        if ($slug) {
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
        foreach ($finder as $file) {
            return $file->getRelativePathname();
        }
        throw new \RuntimeException("Could not find template '${name}'.");
    }
}
