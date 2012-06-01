<?php

namespace Meinhof\Model\Category;

interface CategoryInterface
{
    /**
     * @return string the name of the category
     */
    public function getName();

    /**
     * @return string the unique key of the category
     */
    public function getKey();

    /**
     * @return string the slug of the category
     */
    public function getSlug();

    /**
     * @return string meaningful description
     */
    public function __toString();
}
