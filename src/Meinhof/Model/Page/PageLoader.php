<?php

namespace Meinhof\Model\Page;

use Meinhof\Model\AbstractLoader;

class PageLoader extends AbstractLoader
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
