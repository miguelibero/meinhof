<?php

namespace Meinhof\Model\Page;

use Meinhof\Model\LoaderInterface;

class PageLoader implements LoaderInterface
{
    protected $pages = array();

    public function __construct(array $pages)
    {
        $this->setPages($pages);
    }

    public function getModelName()
    {
        return 'page';
    }

    public function getModelsName()
    {
        return 'pages';
    }

    public function getViewTemplatingKey($model)
    {
        if ($model instanceof PageInterface) {
            return $model->getViewTemplatingKey();
        }
    }

    public function getModel($key)
    {
        $models = $this->getModels();
        foreach ($models as $model) {
            if ($model instanceof PageInterface) {
                if ($model->getKey() == $key) {
                    return $model;
                }
            }
        }
        throw new \RuntimeException("Page with key '${key}' not found.");
    }

    protected function setPages(array $pages)
    {
        $this->pages = array();
        $this->addPages($pages);
    }

    protected function addPages(array $pages)
    {
        foreach ($pages as $k=>$page) {
            if (is_array($page) && !isset($page['key'])) {
                $page['key'] = $k;
            }
            if (!$page instanceof PageInterface) {
                $page = $this->createPage($page);
            }
            if (!$page instanceof PageInterface) {
                throw new \RuntimeException("Invalid page.");
            }
            $this->addPage($page);
        }
    }

    protected function createPage($data)
    {
        if (is_array($data)) {
            return Page::fromArray($data);
        }
    }

    protected function addPage(PageInterface $page)
    {
        $this->pages[$page->getKey()] = $page;
    }

    public function getModels()
    {
        return $this->pages;
    }
}
