<?php

namespace Meinhof\Model\Site;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

interface SiteInterface
{
    /**
     * @return array of site information values
     */
    public function getInfo();
}