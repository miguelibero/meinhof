<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Templating\EngineInterface;

abstract class AbstractPost implements PostInterface
{
    protected $content;
    const EXCERPT_SEPARATOR = '<!-- more -->';

    public function getTitle()
    {
        $title = $this->getSlug();
        $title = str_replace('-', ' ', $title);
        $title = ucwords($title);

        return $title;
    }

    public function getSlug()
    {
        return $this->getKey();
    }

    public function getViewTemplatingKey()
    {
        return 'post';
    }

    public function getExcerpt()
    {
        $parts = explode(self::EXCERPT_SEPARATOR, $this->getContent());
        return reset($parts);
    }

}
