<?php

namespace Meinhof\Assetic;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

use Assetic\Factory\LazyAssetManager;

/**
 * This resource loader implements loading from a list of other loaders
 * Each one assigned to a resource type.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class DelegatingResourceLoader implements ResourceLoaderInterface
{
    protected $parser;
    protected $types = array();
    protected $loaders = array();

    /**
     * @param TemplateNameParserInterface $parser  the parser that gives the resource type from a name
     * @param array                       $loaders a list of resource loaders indexed by type
     */
    public function __construct(TemplateNameParserInterface $parser, array $loaders=array())
    {
        $this->parser = $parser;
        $this->setLoaders($loaders);
    }

    public function setLoaders(array $loaders)
    {
        $this->loaders = array();
        foreach ($loaders as $type=>$loader) {
            if (!$loader instanceof ResourceLoaderInterface) {
                throw new \InvalidArgumentException("Not a valid resource loader.");
            }
            $this->loaders[$type] = $loader;
        }
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
