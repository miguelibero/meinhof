<?php

namespace Meinhof\Model\Page;

abstract class AbstractPage implements PageInterface
{
    public function getTitle()
    {
        $title = $this->getKey();
        $title = str_replace('-', ' ', $title);
        $title = ucwords($title);
        return $title;
    }

    public function getSlug()
    {
        return $this->key;
    }

    public function getViewTemplatingKey()
    {
        $key = $this->getKey();
        if($key){
            return $key;
        }
        return 'page';
    }    
}