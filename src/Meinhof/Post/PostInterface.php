<?php

namespace Meinhof\Post;

interface PostInterface
{
    /**
     * @return string the title of the post
     */
    public function getTitle();

    /**
     * @return \DateTime the last modification time
     */
    public function getUpdated();

    /**
     * @return strin the slug of the post
     */
    public function getSlug();

    /**
     * @return array of globals
     */
    public function getGlobals();

    /**
     * @return string templating key for the post content
     */
    public function getContentTemplatingKey();

    /**
     * @return string templating key for the post view
     */
    public function getViewTemplatingKey();
}