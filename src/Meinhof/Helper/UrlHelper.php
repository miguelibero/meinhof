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

    public function getPostUrl(PostInterface $post)
    {
        $params = array(
            '{slug}'    => $post->getSlug(),
            '{date}'    => $this->formatDate($post->getUpdated()),
        );
        $template = $this->config['post'];
        return strtr($template, $params);
    }

    public function getPageUrl(PageInterface $page)
    {
        $params = array(
            '{slug}'    => $page->getSlug(),
            '{date}'    => $this->formatDate($page->getUpdated()),
        );
        $template = $this->config['page'];
        return strtr($template, $params);
    }
}