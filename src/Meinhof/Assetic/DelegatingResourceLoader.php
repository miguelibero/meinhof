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
    /**
     * the name parser
     *
     * @var TemplateNameParserInterface
     */
    protected $parser;

    /**
     * a cache of types of already loaded resources
     * The array keys are the resource names and the
     * values are the resource types.
     *
     * @var array
     */
    protected $types = array();

    /**
     * the list of resource loaders
     *
     * @var array of ResourceLoaderInterface objects
     */
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

    /**
     * sets the list of available resource loaders
     *
     * @param array $loaders a list of resource loaders indexed by type
     */
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

    /**
     * Adds a new resource loader
     *
     * @param string                  $type   the type for the loader
     * @param ResourceLoaderInterface $loader the resource loader
     */
    public function setLoader($type, ResourceLoaderInterface $loader)
    {
        $this->loaders[$type] = $loader;
    }

    /**
     * Get the resource loader for a given type
     *
     * @param string $type the resource type
     *
     * @return ResourceLoaderInterface the resource loader
     *
     * @throws \InvalidArgumentException if no loader defined for the given type
     */
    protected function getLoader($type)
    {
        if (!isset($this->loaders[$type])) {
            throw new \InvalidArgumentException("No loader defined for type '${type}'.");
        }

        return $this->loaders[$type];
    }

    /**
     * Returns the type of a resource
     *
     * @param  string $name the name of the resource
     * @return string type of the resource
     *
     * @throws \RuntimeException if the resource could not be loaded
     */
    protected function getResourceType($name)
    {
        if (!isset($this->types[$name])) {
            $template = $this->parser->parse($name);
            if ($template instanceof TemplateReferenceInterface) {
                $this->types[$name] = $template->get('engine');
            }
        }
        if (!isset($this->types[$name])) {
            throw new \RuntimeException("Could not load the given resource.");
        }

        return $this->types[$name];
    }

    /**
     * @{inheritDoc}
     */
    public function load($name, LazyAssetManager $mng)
    {
        $type = $this->getResourceType($name);
        $loader = $this->getLoader($type);

        return $loader->load($name, $mng);
    }
}
