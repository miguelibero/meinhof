<?php

namespace Meinhof\Exporter;

use Symfony\Component\EventDispatcher\Event;

class ExportEvent extends Event
{
    protected $url;
    protected $model;
    protected $site;

    public function __construct($url, $model, $site)
    {
        $this->url = $url;
        $this->model = $model;
        $this->site = $site;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getRelativeRoot()
    {
        
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getSite()
    {
        return $this->site;
    }
}