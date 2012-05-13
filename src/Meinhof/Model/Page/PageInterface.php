<?php

namespace Meinhof\Model\Page;

interface PageInterface
{
    /**
     * @return string the key of the page
     */
    public function getKey();

    /**
     * @return string the title of the page
     */
    public function getTitle();

    /**
     * @return \DateTime the last modification time
     */
    public function getUpdated();

    /**
     * @return string the slug of the page
     */
    public function getSlug();

    /**
     * @return array of additional page info
     */
    public function getInfo();

    /**
     * @return string templating key for the page view
     */
    public function getViewTemplatingKey();
}