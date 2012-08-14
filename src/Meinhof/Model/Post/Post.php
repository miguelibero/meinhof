<?php

namespace Meinhof\Model\Post;

use Meinhof\Model\Category\Category;
use Meinhof\Model\Category\CategoryInterface;

class Post extends AbstractPost
{
    protected $slug;
    protected $key;
    protected $title;
    protected $view;
    protected $publish = true;
    protected $updated = null;
    protected $categories;
    protected $info = array();

    public function __construct($key, $slug=null, $updated=null,
        $title=null, $content=null, $view=null, array $info=array(),
        array $categories=array(), $publish=null)
    {
        $this->key = $key;
        $this->slug = $slug;
        $this->title = $title;
        $this->content = $content;
        $this->view = $view;
        if ($updated !== null) {
            $this->setUpdated($updated);
        }
        if ($publish !== null) {
            $this->publish = (bool) $publish;
        }
        $this->setCategories($categories);
        $this->info = $info;
    }

    protected function createCategory($category)
    {
        if (is_string($category)) {
            return new Category($category);
        }
        if (is_array($category)) {
            return Category::fromArray($category);
        }
    }

    protected function setCategories(array $categories)
    {
        $this->categories = array();
        foreach ($categories as $k=>$category) {
            if (is_array($category) && !isset($category['key'])) {
                $category['key'] = $k;
            }
            if (!$category instanceof CategoryInterface) {
                $category = $this->createCategory($category);
            }
            if (!$category instanceof CategoryInterface) {
                throw new \RuntimeException("Invalid category.");
            }
            if (!isset($this->categories[$category->getKey()])) {
                $this->categories[$category->getKey()] = $category;
            }
        }
    }

    protected function setUpdated($updated)
    {
        if (is_int($updated) || is_numeric($updated)) {
            $this->updated = new \DateTime();
            $this->updated->setTimestamp($updated);
        } elseif (is_string($updated)) {
            $this->updated = new \DateTime();
            $this->updated->setTimestamp(strtotime($updated));
        } elseif ($updated instanceof \DateTime) {
            $this->updated = $updated;
        } else {
            throw new \InvalidArgumentException("Could not set updated time '${updated}'.");
        }
    }

    public function getTitle()
    {
        if ($this->title) {
            return $this->title;
        }

        return parent::getTitle();
    }

    public function getPublish()
    {
        return $this->publish;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getSlug()
    {
        if ($this->slug) {
            return $this->slug;
        }

        return parent::getSlug();
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getViewTemplatingKey()
    {
        if ($this->view) {
            return $this->view;
        }

        return parent::getViewTemplatingKey();
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public static function fromArray(array $config)
    {
        if (!isset($config['info']) || !is_array($config['info'])) {
            $config['info'] = array();
        }
        if (!isset($config['categories']) || !is_array($config['categories'])) {
            $config['categories'] = array();
        }
        $config = array_merge(array(
            'key'       => null,
            'slug'      => null,
            'title'     => null,
            'updated'   => null,
            'view'      => null,
            'publish'   => null,
            'content'   => null,
        ), $config);

        return new static($config['key'], $config['slug'],
            $config['updated'], $config['title'], $config['content'], $config['view'],
            $config['info'], $config['categories'], $config['publish']);
    }

}
