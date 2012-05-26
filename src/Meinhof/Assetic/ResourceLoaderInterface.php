<?php

namespace Meinhof\Assetic;

use Assetic\Factory\LazyAssetManager;

interface ResourceLoaderInterface
{
    /**
     * @param string           $name name of the resource to load
     * @param LazyAssetManager $mng  the manager to be loaded
     */
    public function load($name, LazyAssetManager $mng);
}
