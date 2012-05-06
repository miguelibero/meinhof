<?php

namespace Meinhof\Model\Page;

abstract class AbstractPage implements PageInterface
{
    public function getTitle()
    {
        $title = $this->getSlug();
        $title = str_replace('-', ' ', $title);
        $title = ucwords($title);
        return $title;
    }

    
}