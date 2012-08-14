<?php

namespace Meinhof\Model\Category;

use Meinhof\Model\LoaderInterface;
use Meinhof\Model\Post\PostInterface;

class CategoryLoader implements LoaderInterface
{
    protected $categories = array();
    protected $postLoader = null;

    public function __construct(array $categories, LoaderInterface $postLoader=null)
    {
        $this->postLoader = $postLoader;
        $this->addCategories($categories);
        // add categories defined in posts
        if ($this->postLoader) {
            foreach ($this->postLoader->getModels() as $post) {
                if (!$post instanceof PostInterface) {
                    continue;
                }
                $this->addPostCategories($post);
            }
        }
    }

    protected function addPostCategories(PostInterface $post)
    {
        foreach ($post->getCategories() as $cat) {
            if (!$cat instanceof CategoryInterface) {
                continue;
            }
            $data = Category::toArray($cat);
            $cat = $this->createCategory($data);
            $this->addCategory($cat);
        }
    }

    public function getModelName()
    {
        return 'category';
    }

    public function getModelsName()
    {
        return 'categories';
    }

    public function getViewTemplatingKey($model)
    {
        if ($model instanceof CategoryInterface) {
            return $model->getViewTemplatingKey();
        }
    }

    public function getModel($key)
    {
        $models = $this->getModels();
        foreach ($models as $model) {
            if ($model instanceof CategoryInterface) {
                if ($model->getKey() == $key) {
                    return $model;
                }
            }
        }
        throw new \RuntimeException("Category with key '${key}' not found.");
    }

    protected function setCategories(array $categories)
    {
        $this->categories = array();
        $this->addCategories($categories);
    }

    protected function addCategories(array $categories)
    {
        foreach ($categories as $k=>$category) {
            if (is_string($category)) {
                $category = array('key' => $category);
            }
            if (is_array($category) && !isset($category['key'])) {
                $category['key'] = $k;
            }
            if (!$category instanceof CategoryInterface) {
                $category = $this->createCategory($category);
            }
            if (!$category instanceof CategoryInterface) {
                throw new \RuntimeException("Invalid category.");
            }
            $this->addCategory($category);
        }
    }

    protected function createCategory($data)
    {
        if (is_array($data)) {
            if (!isset($data['posts']) && isset($data['key'])) {
                $data['posts'] = $this->getCategoryPosts($data['key']);
            }

            return Category::fromArray($data);
        }
    }

    protected function getCategoryPosts($key)
    {
        $posts = array();
        if ($this->postLoader) {
            foreach ($this->postLoader->getModels() as $post) {
                if (!$post instanceof PostInterface) {
                    continue;
                }
                foreach ($post->getCategories() as $cat) {
                    if (!$cat instanceof CategoryInterface) {
                        continue;
                    }
                    if ($cat->getKey() === $key) {
                        $posts[] = $post;
                    }
                }
            }
        }

        return $posts;
    }

    protected function addCategory(CategoryInterface $category)
    {
        $key = $category->getKey();
        if (isset($this->categories[$key])) {
            // combine categories
            $data = Category::toArray($this->categories[$key]);
            $data = array_merge($data, Category::toArray($category));
            $category = $this->createCategory($data);
        }
        $this->categories[$key] = $category;
    }

    public function getModels()
    {
        return $this->categories;
    }
}
