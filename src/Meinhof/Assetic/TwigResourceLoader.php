<?php

namespace Meinhof\Assetic;

use Assetic\Factory\LazyAssetManager;
use Assetic\Extension\Twig\TwigResource;

/**
 * This resource loader loads twig resources.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class TwigResourceLoader implements ResourceLoaderInterface
{
    /**
     * the twig loader
     *
     * @var \Twig_LoaderInterface
     */
    protected $loader;

    /**
     * @param \Twig_LoaderInterface $loader the twig loader
     */
    public function __construct(\Twig_LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @{inheritDoc}
     */
    public function load($name, LazyAssetManager $mng)
    {
        $resource = new TwigResource($this->loader, $name);
        $mng->addResource($resource, 'twig');
    }
}
