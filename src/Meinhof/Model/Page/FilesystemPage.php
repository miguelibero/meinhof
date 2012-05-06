<?php

namespace Meinhof\Model\Page;

use Symfony\Component\Finder\Finder;

use Meinhof\Model\Site\FilesystemSite;

class FilesystemPage extends Page
{
    protected $site;

    public function setSite(FilesystemSite $site)
    {
        $this->site = $site;
    }

    protected function getSitePath($name)
    {
        return $this->site->getPath($name);
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