<?php

namespace Meinhof\Templating\Storage;

use Symfony\Component\Templating\Storage\Storage;

class FrontMatterStorage extends MatterStorage
{
    protected $storage;
    protected $separator = "/(^|\n)\s*-{3,}\s*\n/";
    protected $content;
    protected $matter;

    public function __construct(Storage $storage, $separator=null)
    {
        $this->storage = $storage;
        if ($separator) {
            $this->separator = $separator;
        }
    }

    protected function load()
    {
        $this->matter = null;
        $this->content = null;
        $content = $this->storage->getContent();
        preg_match_all($this->separator, $content, $matches, PREG_OFFSET_CAPTURE);
        $matches = $matches[0];
        $c = count($matches);
        if ($c == 0) {
            $this->content = $content;
        } else {
            $start = $matches[0][1]+mb_strlen($matches[0][0]);
            if ($c == 1) {
                $this->content = mb_substr($content, $start);
            } else {
                $this->matter = mb_substr($content, $start, $matches[1][1]-$start);
                $start = $matches[1][1]+mb_strlen($matches[1][0]);
                $this->content = mb_substr($content, $start);
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
