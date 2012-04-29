<?php

namespace Meinhof\Templating\Storage;

use Symfony\Component\Templating\Storage\Storage;

class FrontMatterStorage extends Storage
{
    protected $storage;
    protected $separator = "/(^|\n)-{3,}\n/";
    protected $content;
    protected $matter;

    public function __construct(Storage $storage, $separator=null)
    {
        $this->storage = $storage;
        if($separator){
            $this->separator = $separator;
        }
    }

    protected function load()
    {
        $this->matter = null;        
        $this->content = null;
        $content = $this->storage->getContent();
        $parts = preg_split($this->separator, $content);
        
        foreach($parts as $part){
            $part = trim($part);
            if(!$part){
                continue;
            }
            if(!$this->matter){
                $this->matter = $part;
            }else if(!$this->content){
                $this->content = $part;
            }
        }
    }

    public function getMatter()
    {
        $this->load();
        return $this->matter;        
    }

    public function getContent()
    {
        $this->load();
        return $this->content;
    }
}