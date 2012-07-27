<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Definition\Processor;

use Meinhof\Model\Category\Category;
use Meinhof\Model\Category\CategoryInterface;

class Post extends AbstractPost
{
    protected $slug;
    protected $key;
    protected $title;
    protected $view;
    protected $updated = null;
    protected $categories;
    protected $info = array();

    public function __construct($slug, $key, $updated=null,
        $title=null, $view=null, array $info=array(), array $categories=array())
    {
        $this->slug = $slug;
        $this->key = $key;
        $this->title = $title;
        $this->view = $view;
        if ($updated !== null) {
            $this->setUpdated($updated);
        }
        $this->setCategories($categories);
        $this->info = $info;
    }

    protected function setCategories(array $categories)
    {
        $this->categories = array();
        foreach ($categories as $category) {
            if(is_string($category)){
                $category = new Category($category);
            }
            if (is_array($category)) {
                $category = Category::fromArray($category);
            }
            if (!$category instanceof CategoryInterface) {
                throw new \RuntimeException("Invalid category.");
            }
            $this->categories[] = $category;
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

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getInfo()
    {
        return $this->info;
    }

    protected function getContentTemplatingKey()
    {
        return $this->key;
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

    public static function fromArray(array $config, LoaderInterface $loader=null)
    {
        if ($loader && isset($config['key'])) {
            $matter = self::loadMatter($config['key'], $loader);
            if ($matter) {
                $config = array_merge($matter, $config);
            }
        }

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
            'view'      => null
        ), $config);

        return new static($config['slug'], $config['key'],
            $config['updated'], $config['title'], $config['view'],
            $config['info'], $config['categories']);
    }

    protected static function loadMatter($key, LoaderInterface $loader)
    {
        if (!$loader->supports($key)) {
            return null;
        }
        $matter = $loader->load($key);
        if (!is_array($matter)) {
            return null;
        }
        $matter = array('post' => $matter);
        $processor = new Processor();
        $configuration = new PostMatterConfiguration();

        return $processor->processConfiguration($configuration, $matter);
    }
}
