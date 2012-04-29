<?php

namespace Meinhof\Loader;

use Symfony\Component\Templating\Loader\FilesystemLoader as BaseFilesystemLoader;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * Overwritten to accept a list of base directories
 */
class FilesystemLoader extends BaseFilesystemLoader
{
    protected $paths;

    public function __construct($paths)
    {
        $this->paths = (array) $paths;
        $patterns = array();
        foreach($this->paths as $path){
            $patters[] = $path.'/*';
        }
        parent::__construct($patterns);
    }

    public function load(TemplateReferenceInterface $template)
    {
        $file = $template->get('name');
        foreach($this->paths as $path){
            $template->set('name', $path.'/'.$file);
            $result = parent::load($template);
            if($result){
                return $result;
            }
        }
    }
}