<?php

namespace Meinhof\Helper;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;
use Meinhof\Model\Category\CategoryInterface;

interface UrlHelperInterface
{
    public function getUrl($model, array $parameters);
}