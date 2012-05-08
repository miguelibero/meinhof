<?php

namespace Meinhof\Exporter;

use Meinhof\Model\Site\SiteInterface;
use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

use Meinhof\Helper\UrlHelperInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractExporter implements ExporterInterface
{
    protected $templating;
    protected $url_helper;
    protected $event_dispatcher;

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
        if(!$this->templating->exists($key)){
            throw new \InvalidArgumentException("View template '${key}' does not exist.");
        }
        if(!$this->templating->supports($key)){
            throw new \InvalidArgumentException("View template '${key}' does not have a valid format.");
        }
        return $this->templating->render($key, $params);
    }

    protected function exportUrl($url, $key, array $params)
    {
        $content = $this->render($key, $params);
        $this->saveUrl($url, $content);
    }

    protected function getSiteParams(SiteInterface $site)
    {
        $params = $site->getGlobals();
        $params['posts'] = $site->getPosts();
        $params['pages'] = $site->getPages();
        $params['categories'] = $site->getCategories();

        return $params;
    }

    protected function getModelUrl($model)
    {
        if($model instanceof PostInterface){
            $url = $this->url_helper->getPostUrl($model);
        }else if($model instanceof PageInterface){
            $url = $this->url_helper->getPageUrl($model);
        }else{
            throw \RuntimeException("unknown model type");
        }
        $url = trim(parse_url($url, PHP_URL_PATH),'/');
        return $url;
    }

    protected function getPageUrl(PostInterface $post)
    {
        $url = $this->url_helper->getPageUrl($post);
        return $this->fixRelativeUrl($url);
    }

    protected function dispatchEvent($url, $model, $site)
    {
        if(!$this->event_dispatcher){
            return;
        }
        $event = new ExportEvent($url, $model, $site);
        $this->event_dispatcher->dispatch('export', $event);
    }

    public function exportPost(PostInterface $post, SiteInterface $site)
    {
        $url = $this->getModelUrl($post);
        $this->dispatchEvent($url, $post, $site);

        $params = $this->getSiteParams($site);
        $params['post'] = $post;        
        $key = $post->getViewTemplatingKey();
        $this->exportUrl($url, $key, $params);
    }

    public function exportPage(PageInterface $page, SiteInterface $site)
    {
        $url = $this->getModelUrl($page);
        $this->dispatchEvent($url, $page, $site);

        $params = $this->getSiteParams($site);
        $params['page'] = $page;
        $key = $page->getViewTemplatingKey();
        $this->exportUrl($url, $key, $params);
    }
}