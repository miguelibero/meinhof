<?php

namespace Meinhof\Templating\Storage;

use Symfony\Component\Templating\Storage\Storage;

class FrontMatterStorage extends MatterStorage
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
        $parts = array_map('trim', $parts);
        $parts = array_values(array_filter($parts));
        $c = count($parts);
        if($c > 1){
            $this->matter = $parts[0];
            $this->content = $parts[1];
        }else if($c > 0){
            $this->content = $parts[0];
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