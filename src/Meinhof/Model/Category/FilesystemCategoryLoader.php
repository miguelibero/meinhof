<?php

namespace Meinhof\Model\Category;

use Symfony\Component\Finder\Finder;

use Meinhof\Model\LoaderInterface;

class FilesystemCategoryLoader extends CategoryLoader
{
    protected $viewsPath;

    public function __construct(array $categories, LoaderInterface $postLoader, $viewsPath)
    {
        $this->viewsPath = $viewsPath;
        parent::__construct($categories, $postLoader);
    }

    protected function createCategory($data)
    {
        if(is_array($data)){
            if(!isset($data['view'])){
                $data['view'] = 'category';
            }
            $data['view'] = $this->findView($data['view']);
        }
        return parent::createCategory($data);
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