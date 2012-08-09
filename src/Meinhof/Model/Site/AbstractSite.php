<?php

namespace Meinhof\Model\Site;

use Meinhof\Model\Category\CategoryInterface;
use Meinhof\Model\Post\PostInterface;

abstract class AbstractSite implements SiteInterface
{
    public function getCategories()
    {
        $categories = array();
        foreach($this->getPosts() as $post){
            if(!$post instanceof PostInterface) {
                continue;
            }            
            foreach($post->getCategories() as $category){
                if(!$category instanceof CategoryInterface) {
                    continue;
                }
                if(!isset($categories[$category->getKey()])){
                    $categories[$category->getKey()] = $category;
                }
            }
        }
        return $categories;
    }
}
