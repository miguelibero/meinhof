<?php

namespace Meinhof\Model\Site;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

interface SiteInterface
{
    /**
     * @return array of PostInterface objects
     */
    public function getPosts();

    /**
     * @return array of PageInterface objects
     */
    public function getPages();

    /**
     * @return array of CategoriesInterface objects
     */
    public function getCategories();

    /**
     * @return array of view keys
     */
    public function getViews();

    /**
     * @return array of site information values
     */
    public function getInfo();
}
