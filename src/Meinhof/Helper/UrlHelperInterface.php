<?php

namespace Meinhof\Helper;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;
use Meinhof\Model\Category\CategoryInterface;

interface UrlHelperInterface
{
    public function getPostUrl(PostInterface $post);

    public function getPageUrl(PageInterface $page);

    public function getCategoryUrl(CategoryInterface $cat);

    public function setParameter($name, $value);
}
