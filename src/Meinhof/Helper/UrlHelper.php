<?php

namespace Meinhof\Helper;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

class UrlHelper implements UrlHelperInterface
{
    protected $config = array(
        'post'  => 'post/{date}/{slug}.html',
        'page'  => '{slug}.html',
        'date'  => 'Y-m-d',
    );
    protected $parameters = array();

    const PARAMETER_TEMPLATE = '{%s}';

    public function __construct(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    protected function formatDate($date)
    {
        if($date instanceof \DateTime){
            return $date->format($this->config['date']);
        }
    }

    protected function getPostParameters(PostInterface $post)
    {
        return array(
            'slug'    => $post->getSlug(),
            'date'    => $this->formatDate($post->getUpdated()),
        );
    }

    protected function getPageParameters(PageInterface $page)
    {
        return array(
            'slug'    => $page->getSlug(),
            'date'    => $this->formatDate($page->getUpdated()),
        );
    }

    public function generateUrl($name, array $params)
    {
        if(!isset($this->config[$name])){
            throw new \InvalidArgumentException("Unknown url template '${name}'.");
        }
        $tparams = array();
        foreach($params as $k=>$v){
            $k = sprintf(self::PARAMETER_TEMPLATE, $k);
            $tparams[$k] = $v;
        }
        return strtr($this->config[$name], $tparams);
    }

    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function getPostUrl(PostInterface $post)
    {
        $params = array_merge($this->parameters,
            $this->getPostParameters($post));
        return $this->generateUrl('post', $params);
    }

    public function getPageUrl(PageInterface $page)
    {
        $params = array_merge($this->parameters,
            $this->getPageParameters($page));
        return $this->generateUrl('page', $params);
    }
}