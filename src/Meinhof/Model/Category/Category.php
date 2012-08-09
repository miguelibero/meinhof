<?php

namespace Meinhof\Model\Category;

class Category extends AbstractCategory
{
    protected $posts = array();

    public function __construct($key, $name=null, $slug=null, array $posts=array())
    {
        $this->setPosts($posts);
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

    public static function fromArray(array $config)
    {
        $config = array_merge(array(
            'key'   => null,
            'name'  => null,
            'slug'  => null
        ), $config);

        if (!isset($config['posts']) || !is_array($config['posts'])) {
            $config['posts'] = array();
        }

        return new static($config['key'], $config['name'], $config['slug'], $config['posts']);
    }
}
