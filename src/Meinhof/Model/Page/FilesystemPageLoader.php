<?php

namespace Meinhof\Model\Page;

use Symfony\Component\Finder\Finder;

class FilesystemPageLoader extends PageLoader
{
    protected $viewsPath;

    public function __construct(array $pages, $viewsPath)
    {
        $this->viewsPath = $viewsPath;
        parent::__construct($pages);
    }

    protected function createPage($data)
    {
        if(is_array($data)){
            if(!isset($data['view'])){
                $data['view'] = isset($data['key']) ? $data['key'] : 'page';
            }
            $data['view'] = $this->findView($data['view']);
        }
        return parent::createPage($data);
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