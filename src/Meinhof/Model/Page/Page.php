<?php

namespace Meinhof\Model\Page;

class Page extends AbstractPage
{
    protected $key;
    protected $slug;
    protected $updated;
    protected $title;
    protected $view;
    protected $info;
    protected $publish = false;

    public function __construct($key, $slug=null, $updated=null, $title=null,
        $view=null, array $info=array(), $publish=null)
    {
        $this->key = $key;
        $this->slug = $slug;
        if ($updated !== null) {
            $this->setUpdated($updated);
        }
        $this->title = $title;
        $this->view = $view;
        $this->info = $info;
        if ($publish !== null) {
            $this->publish = (bool) $publish;
        }
    }

    protected function setUpdated($updated)
    {
        if (is_int($updated) || is_numeric($updated)) {
            $this->updated = new \DateTime();
            $this->updated->setTimestamp($updated);
        } elseif (is_string($updated)) {
            $this->updated = new \DateTime($updated);
        } elseif ($updated instanceof \DateTime) {
            $this->updated = $updated;
        } else {
            throw new \InvalidArgumentException("Could not set updated time.");
        }
    }

    public function getPublish()
    {
        return $this->publish;
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
        if ($this->slug) {
            return $this->slug;
        }

        return parent::getSlug();
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getViewTemplatingKey()
    {
        if ($this->view) {
            return $this->view;
        }
        if ($this->slug) {
            return $this->slug;
        }

        return parent::getViewTemplatingKey();
    }

    public static function fromArray(array $data)
    {
        if (!isset($data['key'])) {
            throw new \InvalidArgumentException("Each page neeeds a key.");
        }
        if (!isset($data['info']) || !is_array($data['info'])) {
            $data['info'] = array();
        }
        $data = array_merge(array(
            'key'       => null,
            'slug'      => null,
            'updated'   => null,
            'title'     => null,
            'view'      => null,
            'publish'   => null
        ), $data);

        return new static($data['key'], $data['slug'], $data['updated'],
            $data['title'], $data['view'], $data['info'], $data['publish']);
    }

}
