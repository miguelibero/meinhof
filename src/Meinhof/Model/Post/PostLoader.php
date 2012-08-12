<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Templating\EngineInterface;

use Meinhof\Model\LoaderInterface;

class PostLoader implements LoaderInterface
{
    protected $templating;
    protected $posts = array();

    public function __construct(array $posts, EngineInterface $templating)
    {
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
        if($model instanceof PostInterface){
            return $model->getViewTemplatingKey();
        }
    }

    public function getModel($key)
    {
        $models = $this->getModels();
        foreach($models as $model){
            if($model instanceof PostInterface){
                if($model->getKey() == $key){
                    return $model;
                }
            }
        }
        throw new \RuntimeException("Post with key '${key}' not found.");
    }

    protected function renderContent($key, array $params)
    {
        if (!$this->templating) {
            throw new \RuntimeException("No templating engine loaded");
        }
        if (!$this->templating->exists($key)) {
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
        if(is_array($data)){
            if(!isset($data['content']) && isset($data['key'])){
                $template = $data['key'];
                $data['content'] = $this->renderContent($template, $data);
            }
            return Post::fromArray($data);
        }
    }

    protected function addPost(PostInterface $post)
    {
        $this->posts[$post->getKey()] = $post;  
    }

    public function getModels()
    {
        return $this->posts;
    } 
}