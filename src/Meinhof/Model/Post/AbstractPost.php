<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Templating\EngineInterface;

abstract class AbstractPost implements PostInterface
{
    protected $templating;
    protected $content;
    const EXCERPT_SEPARATOR = '<!-- more -->';

    public function getTitle()
    {
        $title = $this->getSlug();
        $title = str_replace('-', ' ', $title);
        $title = ucwords($title);
        return $title;
    }

    public function getViewTemplatingKey()
    {
        return 'post';
    }      

    public function setTemplatingEngine(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    abstract protected function getContentTemplatingKey();

    public function getContent()
    {
        if(!$this->content){
            $this->content = $this->renderContent();
        }
        return $this->content;
    }

    public function getExcerpt()
    {
        $parts = explode(self::EXCERPT_SEPARATOR, $this->getContent());
        return reset($parts);
    }

    protected function getContentTemplateParameters()
    {
        return array('post'=>$this);
    }

    public function renderContent()
    {
        $key = $this->getContentTemplatingKey();
        if(!$this->templating){
            throw new \RuntimeException("No templating engine loaded");
        }
        if(!$this->templating->exists($key)){
            throw new \RuntimeException("Post template '${key}' does not exist.");
        }
        if(!$this->templating->supports($key)){
            throw new \RuntimeException("Post template '${key}' does not have a valid format.");   
        }
        return $this->templating->render($key, $this->getContentTemplateParameters());
    }

}
