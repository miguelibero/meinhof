<?php

namespace Meinhof\Assetic;

use Assetic\Factory\LazyAssetManager;
use Assetic\Extension\Twig\TwigResource;

class TwigResourceLoader implements ResourceLoaderInterface
{
    protected $loader;

    public function __construct(\Twig_LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function load($name, LazyAssetManager $mng)
    {
        $resource = new TwigResource($this->loader, $name);
        $mng->addResource($resource, 'twig');
    }
}