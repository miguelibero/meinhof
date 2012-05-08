<?php

namespace Meinhof\Model\Post;

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
     * @return string the slug of the post
     */
    public function getSlug();

    /**
     * @return array of additional post info
     */
    public function getInfo();

    /**
     * @return string the post content
     */
    public function getContent();

    /**
     * @return string the post excerpt
     */
    public function getExcerpt();

    /**
     * @return array of CategoryInterface objects
     */
    public function getCategories();

    /**
     * @return string templating key for the post view
     */
    public function getViewTemplatingKey();
}