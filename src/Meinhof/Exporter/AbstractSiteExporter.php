<?php

namespace Meinhof\Exporter;

use Meinhof\Model\Site\SiteInterface;
use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;
use Meinhof\Model\Category\CategoryInterface;

use Meinhof\Helper\UrlHelperInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractSiteExporter implements SiteExporterInterface
{
    protected $templating;
    protected $url_helper;
    protected $event_dispatcher;
    protected $parameters = array();

    public function __construct(EngineInterface $engine,
        UrlHelperInterface $url)
    {
        $this->templating = $engine;
        $this->url_helper = $url;
    }

    abstract protected function saveUrl($url, $content);

    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->event_dispatcher = $dispatcher;
    }

    protected function render($key, array $params)
    {
        if (!$this->templating->exists($key)) {
            throw new \InvalidArgumentException("View template '${key}' does not exist.");
        }
        if (!$this->templating->supports($key)) {
            throw new \InvalidArgumentException("View template '${key}' does not have a valid format.");
        }

        return $this->templating->render($key, $params);
    }

    protected function exportUrl($url, $key, array $params)
    {
        $params['webroot'] = ExportEvent::getRelativeRootUrl($url);
        $content = $this->render($key, $params);
        $this->saveUrl($url, $content);
    }

    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    protected function getSiteParameters(SiteInterface $site)
    {
        return array_merge(array(
            'site' => $site
        ), $this->parameters);
    }

    protected function getModelUrl($model)
    {
        if ($model instanceof PostInterface) {
            $url = $this->url_helper->getPostUrl($model);
        } elseif ($model instanceof PageInterface) {
            $url = $this->url_helper->getPageUrl($model);
        } elseif ($model instanceof CategoryInterface) {
            $url = $this->url_helper->getCategoryUrl($model);            
        } else {
            throw new \RuntimeException("unknown model type '".get_class($model)."'.");
        }
        $url = trim(parse_url($url, PHP_URL_PATH),'/');

        return $url;
    }

    protected function getPageUrl(PostInterface $post)
    {
        $url = $this->url_helper->getPageUrl($post);

        return $this->fixRelativeUrl($url);
    }

    protected function dispatchEvent($url, $model, $site, $name)
    {
        if (!$this->event_dispatcher) {
            return;
        }
        $event = new ExportEvent($url, $model, $site);
        $this->event_dispatcher->dispatch($name, $event);
    }

    public function exportPost(PostInterface $post, SiteInterface $site)
    {
        $url = $this->getModelUrl($post);
        $this->dispatchEvent($url, $post, $site, 'before_export');

        $params = $this->getSiteParameters($site);
        $params['post'] = $post;
        $key = $post->getViewTemplatingKey();
        $this->exportUrl($url, $key, $params);
        $this->dispatchEvent($url, $post, $site, 'after_export');
    }

    public function exportPage(PageInterface $page, SiteInterface $site)
    {
        $url = $this->getModelUrl($page);
        $this->dispatchEvent($url, $page, $site, 'before_export');

        $params = $this->getSiteParameters($site);
        $params['page'] = $page;
        $key = $page->getViewTemplatingKey();
        $this->exportUrl($url, $key, $params);
        $this->dispatchEvent($url, $page, $site, 'after_export');
    }
    
    public function exportCategory(CategoryInterface $category, SiteInterface $site)
    {
        $url = $this->getModelUrl($category);
        $this->dispatchEvent($url, $category, $site, 'before_export');

        $params = $this->getSiteParameters($site);
        $params['category'] = $category;
        $key = $category->getViewTemplatingKey();
        $this->exportUrl($url, $key, $params);
        $this->dispatchEvent($url, $category, $site, 'after_export');
    }    
}
