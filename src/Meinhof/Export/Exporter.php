<?php

namespace Meinhof\Export;

use Meinhof\Helper\UrlHelperInterface;
use Meinhof\Templating\Finder\FinderInterface;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Exporter implements ExporterInterface
{
    protected $templating;
    protected $urlHelper;
    protected $store;
    protected $dispatcher;
    protected $finder;

    public function __construct(EngineInterface $engine, UrlHelperInterface $url, StoreInterface $store,
        FinderInterface $finder=null, EventDispatcherInterface $dispatcher=null)
    {
        $this->templating = $engine;
        $this->store = $store;
        $this->urlHelper = $url;
        $this->finder = $finder;        
        $this->dispatcher = $dispatcher;
    }

    protected function render($key, array $params)
    {
        if($this->finder){
            $key = $this->finder->find($key);
        }
        if (!$this->templating->exists($key)) {
            throw new \InvalidArgumentException("View template '${key}' does not exist.");
        }
        if (!$this->templating->supports($key)) {
            throw new \InvalidArgumentException("View template '${key}' does not have a valid format.");
        }

        return $this->templating->render($key, $params);
    }

    protected function getUrl($model, array $params)
    {
        $url = $this->urlHelper->getUrl($model, $params);
        $url = trim(parse_url($url, PHP_URL_PATH),'/');

        return $url;
    }

    public function export($model, $template, array $params)
    {
        $url = $this->getUrl($model, $params);
        $params['webroot'] = ExportEvent::getRelativeRootUrl($url);
        if ($this->dispatcher) {
            $this->dispatcher->dispatch('export', new ExportEvent($url, $model, $params));
        }
        $content = $this->render($template, $params);
        $this->store->store($url, $content);
    }
}
