<?php

namespace Meinhof\Model\Page;

class Page extends AbstractPage
{
    protected $slug;
    protected $updated;
    protected $title;
    protected $view;
    protected $info;

    public function __construct($slug, $updated=null, $title=null, $view=null, array $info=array())
    {
        $this->slug = $slug;
        if($updated !== null){
            $this->setUpdated($updated);
        }
        $this->title = $title;
        $this->view = $view;
        $this->info = $info;
    }


    protected function setUpdated($updated)
    {
        if(is_int($updated) || is_numeric($updated)){
            $this->updated = new \DateTime();
            $this->updated->setTimestamp($updated);
        }else if(is_string($updated)){
            $this->updated = new \DateTime($updated);
        }else if($updated instanceof \DateTime){
            $this->updated = $updated;
        }else{
            throw new \InvalidArgumentException("Could not set updated time.");
        }
    }    

    public function getTitle()
    {
        if($this->title){
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

    public function getViewTemplatingKey()
    {
        if($this->view){
            return $this->view;
        }
        if($this->slug){
            return $this->slug;
        }
        return 'page';
    }

    public static function fromArray(array $data)
    {
        if(!isset($data['info']) || !is_array($data['info'])){
            $data['info'] = array();
        }
        $data = array_merge(array(
            'slug'      => null,
            'updated'   => null,
            'title'     => null,
            'view'      => null,
        ), $data);
        return new static($data['slug'], $data['updated'], $data['title'], $data['view'], $data['info']);
    }

}