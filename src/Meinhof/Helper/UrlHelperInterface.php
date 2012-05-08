<?php

namespace Meinhof\Helper;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

interface UrlHelperInterface
{
    public function getPostUrl(PostInterface $post);
    
    public function getPageUrl(PageInterface $page);
}