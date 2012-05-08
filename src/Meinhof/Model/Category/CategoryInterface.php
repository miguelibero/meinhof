<?php

namespace Meinhof\Model\Category;


interface CategoryInterface
{
    /**
     * @return string the name of the category
     */
    public function getName();

    /**
     * @return string meaningful description
     */
    public function __toString();
}