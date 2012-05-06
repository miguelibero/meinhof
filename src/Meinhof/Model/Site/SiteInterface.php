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
     * @return array of key values to replace in the templates
     */
    public function getGlobals();

    /**
     * @param PostInterface $post post to save
     * @param string $content generated post content to save
     */
    public function savePost(PostInterface $post, $content);

    /**
     * @param PageInterface $page page to save
     * @param string $content generated page content to save
     */
    public function savePage(PageInterface $page, $content);    
}