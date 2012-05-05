<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Templating\EngineInterface;

use Meinhof\Post\PostInterface;
use Meinhof\Site\SiteInterface;

class UpdatePostsAction extends OutputAction
{
    protected $site;
    protected $templating_post;
    protected $templating_view;
    protected $output;

    public function __construct(SiteInterface $site, EngineInterface $templating_post,
        EngineInterface $templating_view, OutputInterface $output=null)
    {
        $this->site = $site;
        $this->templating_post = $templating_post;
        $this->templating_view = $templating_view;
        $this->output = $output;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    public function take()
    {
        $posts = $this->site->getPosts();

        $globals = $this->site->getGlobals();
        $globals['posts'] = $posts;

        $this->writeOutputLine(sprintf("updating %d posts...", count($posts)), 2);

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
            $this->writeOutput(sprintf(".", count($posts)), 1);
        }
        $this->writeOutputLine("", 1);
        $this->writeOutputLine("done", 2);
    }
}