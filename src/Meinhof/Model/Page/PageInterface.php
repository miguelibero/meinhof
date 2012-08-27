<?php

namespace Meinhof\Model\Page;

interface PageInterface
{
    /**
     * @return string the key of the page
     */
    public function getKey();

    /**
     * @return string the url of the page
     */
    public function getUrl();    

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
     * @return Boolean if the page should be published
     */
    public function getPublish();

    /**
     * @return array of additional page info
     */
    public function getInfo();

    /**
     * @return string templating key for the page view
     */
    public function getViewTemplatingKey();
}
