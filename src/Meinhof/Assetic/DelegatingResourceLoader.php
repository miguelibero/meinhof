<?php

namespace Meinhof\Assetic;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

use Assetic\Factory\LazyAssetManager;

class DelegatingResourceLoader implements ResourceLoaderInterface
{
    protected $parser;
    protected $types = array();
    protected $loaders = array();

    public function __construct(TemplateNameParserInterface $parser, array $loaders=array())
    {
        $this->parser = $parser;
        $this->loaders = $loaders;
    }

    public function setLoader($type, ResourceLoaderInterface $loader)
    {
        $this->loaders[$type] = $loader;
    }

    protected function getLoader($type)
    {
        if (!isset($this->loaders[$type])) {
            throw new \InvalidArgumentException("No loader defined for type '${type}'.");
        }

        return $this->loaders[$type];
    }

    protected function getResourceType($name)
    {
        if (!isset($this->types[$name])) {
            $template = $this->parser->parse($name);
            if ($template instanceof TemplateReferenceInterface) {
                $this->types[$name] = $template->get('engine');
            }
        }
        if (!isset($this->types[$name])) {
            throw new \InvalidArgumentException("Could not load the given resource.");
        }

        return $this->types[$name];
    }

    public function load($name, LazyAssetManager $mng)
    {
        $type = $this->getResourceType($name);
        $loader = $this->getLoader($type);

        return $loader->load($name, $mng);
    }
}
