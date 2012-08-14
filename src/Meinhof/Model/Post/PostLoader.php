<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Templating\EngineInterface;

use Meinhof\Model\LoaderInterface;
use Meinhof\Templating\Finder\FinderInterface;

class PostLoader implements LoaderInterface
{
    protected $templating;
    protected $finder;
    protected $posts = array();

    public function __construct(array $posts,
        EngineInterface $templating, FinderInterface $finder=null)
    {
        $this->finder = $finder;
        $this->templating = $templating;
        $this->setPosts($posts);
    }

    public function getModelName()
    {
        return 'post';
    }

    public function getModelsName()
    {
        return 'posts';
    }

    public function getViewTemplatingKey($model)
    {
        if ($model instanceof PostInterface) {
            return $model->getViewTemplatingKey();
        }
    }

    public function getModel($key)
    {
        $models = $this->getModels();
        foreach ($models as $model) {
            if ($model instanceof PostInterface) {
                if ($model->getKey() == $key) {
                    return $model;
                }
            }
        }
        throw new \RuntimeException("Post with key '${key}' not found.");
    }

    protected function renderContent($key, array $params)
    {
        if ($this->finder) {
            $key = $this->finder->find($key);
        }
        if (!$this->templating) {
            throw new \RuntimeException("No templating engine loaded");
        }
        if (!$this->templating->exists($key)) {
            print_r($this->templating);
            throw new \RuntimeException("Post template '${key}' does not exist.");
        }
        if (!$this->templating->supports($key)) {
            throw new \RuntimeException("Post template '${key}' does not have a valid format.");
        }

        return $this->templating->render($key, $params);
    }

    protected function setPosts(array $posts)
    {
        $this->posts = array();
        $this->addPosts($posts);
    }

    protected function addPosts(array $posts)
    {
        foreach ($posts as $k=>$post) {
            if (is_array($post) && !isset($post['key'])) {
                $post['key'] = $k;
            }
            if (!$post instanceof PostInterface) {
                $post = $this->createPost($post);
            }
            if (!$post instanceof PostInterface) {
                throw new \RuntimeException("Invalid post.");
            }
            $this->addPost($post);
        }
    }

    protected function createPost($data)
    {
        if (is_array($data)) {
            if (!isset($data['content']) && isset($data['key'])) {
                $template = $data['key'];
                $data['content'] = $this->renderContent($template, $data);
            }

            return Post::fromArray($data);
        }
    }

    protected function addPost(PostInterface $post)
    {
        $this->posts[$post->getKey()] = $post;
        uksort($this->posts, function($a, $b){
            if (!$a instanceof PostInterface) {
                return -1;
            }
            if (!$b instanceof PostInterface) {
                return 1;
            }
            $ua = $a->getUpdated();
            $ub = $b->getUpdated();

            return  $ua === $ub ? 0 : ($ua > $ub ? 1 : -1);
        });
    }

    public function getModels()
    {
        return $this->posts;
    }
}
