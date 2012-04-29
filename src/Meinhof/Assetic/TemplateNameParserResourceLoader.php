<?php

namespace Meinhof\Assetic;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Assetic\Extension\Twig\TwigResource;

class TemplateNameParserResourceLoader implements ResourceLoader
{
    protected $parser;
    protected $twig_loader;
    protected $types = array();

    public function __construct(TemplateNameParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function setTwigLoader(\Twig_LoaderInterface $loader)
    {
        $this->twig_loader = $loader;
    }

    public function getResourceType($name)
    {
        if(!isset($this->types[$name])){
            $template = $this->parser->parse($name);
            $this->types[$name] = $template->get('engine');
        }
        return $this->types[$name];
    }

    public function getResource($name)
    {
        $type = $this->getResourceType($name);
        switch($type){
            case 'twig':
                return new TwigResource($this->twig_loader, $name);
        }
        return null;
    }
}