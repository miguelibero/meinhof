<?php

namespace Meinhof\Model\Category;

class Category extends AbstractCategory
{
    protected $publish = null;
    protected $view = null;
    protected $posts = array();

    public function __construct($key, $name=null, $slug=null,
        array $posts=array(), $view=null, $publish=null)
    {
        $this->setPosts($posts);
        if($publish !== null){
            $this->publish = (bool) $publish;
        }
        $this->view = $view;
        parent::__construct($key, $name, $slug);
    }

    public function setPosts(array $posts)
    {
        $this->posts = $posts;
    }

    public function getPosts()
    {
        return $this->posts;
    }

    public function getPublish()
    {
        if($this->publish !== null){
            return $this->publish;
        }
        return parent::getPublish();
    }

    public function getViewTemplatingKey()
    {
        if($this->view){
            return $this->view;
        }
        return parent::getViewTemplatingKey();
    }

    static function toArray(CategoryInterface $category)
    {
        return array(
            'key'       => $category->getKey(),
            'name'      => $category->getName(),
            'slug'      => $category->getSlug(),
            'publish'   => $category->getPublish(),
            'view'      => $category->getViewTemplatingKey(),
        );
    }

    public static function fromArray(array $config)
    {
        $config = array_merge(array(
            'key'       => null,
            'name'      => null,
            'slug'      => null,
            'publish'   => null,
            'view'      => null,
        ), $config);

        if (!isset($config['posts']) || !is_array($config['posts'])) {
            $config['posts'] = array();
        }

        return new static($config['key'], $config['name'],
            $config['slug'], $config['posts'], $config['view'], $config['publish']);
    }
}
