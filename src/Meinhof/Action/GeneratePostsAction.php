<?php

namespace Meinhof\Action;

use Symfony\Component\Templating\EngineInterface;

use Meinhof\Post\PostInterface;
use Meinhof\Site\SiteInterface;

class GeneratePostsAction implements ActionInterface
{
    protected $site;
    protected $templating_post;
    protected $templating_view;

    public function __construct(SiteInterface $site, EngineInterface $templating_post, EngineInterface $templating_view)
    {
        $this->site = $site;
        $this->templating_post = $templating_post;
        $this->templating_view = $templating_view;
    }

    public function getEventName()
    {
        return 'generate';
    }

    public function getName()
    {
        return 'posts';
    }    

    public function take()
    {
        $posts = $this->site->getPosts();

        $globals = $this->site->getGlobals();
        $globals['posts'] = $posts;

        foreach($posts as $post){
            if(!$post instanceof PostInterface){
                continue;
            }
            // render post content
            $ckey = $post->getContentTemplatingKey();
            if(!$this->templating_post->exists($ckey)){
                throw new \InvalidArgumentException("Post template '${ckey}' does not exist.");
            }
            if(!$this->templating_post->supports($ckey)){
                throw new \InvalidArgumentException("Post template '${ckey}' does not have a valid format.");   
            }
            $params = $globals;
            $params['post'] = $post;
            $content = $this->templating_post->render($ckey, $params);

            // render post view
            $params['content'] = $content;
            $vkey = $post->getViewTemplatingKey();
            if($vkey){
                if(!$this->templating_view->exists($vkey)){
                    throw new \InvalidArgumentException("View template '${vkey}' does not exist.");
                }
                if(!$this->templating_view->supports($vkey)){
                    throw new \InvalidArgumentException("View template '${vkey}' does not have a valid format.");
                }            
                $content = $this->templating_view->render($vkey, $params);
            }

            $this->site->savePost($post, $content);
        }
    }
}