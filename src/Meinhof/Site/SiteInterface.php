<?php

namespace Meinhof\Site;

use Meinhof\Post\PostInterface;

interface SiteInterface
{
    /**
     * @return array of PostInterface objects
     */
    public function getPosts();

    /**
     * @return array of templating keys
     */
    public function getTemplates();

    /**
     * @return array of key values to replace in the templates
     */
    public function getGlobals();

    /**
     * @param PostInterface $post post to save
     * @param string $content generated post content to save
     */
    public function savePost(PostInterface $post, $content);
}