<?php

namespace Meinhof\Export;

use Symfony\Component\EventDispatcher\Event;

class ExportEvent extends Event
{
    protected $url;
    protected $model;
    protected $parameters;

    public function __construct($url, $model, array $params)
    {
        $this->url = $url;
        $this->model = $model;
        $this->parameters = $params;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getRelativeRoot()
    {
        return self::getRelativeRootUrl($this->getUrl());
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public static function getRelativeRootUrl($url)
    {
        $times = count(explode('/', $url))-1;
        return str_repeat('../', $times);
    }
}