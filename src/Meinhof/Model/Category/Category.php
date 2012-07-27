<?php

namespace Meinhof\Model\Category;

class Category implements CategoryInterface
{
    protected $key;
    protected $name;
    protected $slug;

    public function __construct($key, $name=null, $slug=null)
    {
        $this->key = $key;
        if($name){
            $this->name = $name;
        }
        if($slug){
            $this->slug = $slug;
        }
    }

    public function getName()
    {
        if ($this->name) {
            return $this->name;
        }

        return $this->getKey();
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getSlug()
    {
        if ($this->slug) {
            return $this->slug;
        }
        $slug = mb_strtolower($this->getName());
        $slug = preg_replace('/[^a-z0-9]/', '-', $slug);

        return $slug;
    }

    public function getPosts()
    {
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

    public static function fromArray(array $config)
    {
        $config = array_merge(array(
            'key'   => null,
            'name'  => null,
            'slug'  => null
        ), $config);

        return new Category($config['key'], $config['name'], $config['slug']);
    }

}
