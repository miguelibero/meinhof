<?php

namespace Meinhof\Assetic;

use Assetic\Factory\LazyAssetManager;

/**
 * This interface defines a class that can load resources into a
 * assetic LazyAssetManager.
 *
 * Inside the load function, the implemented loader should generate
 * a ResourceInterface object and load it into the manager.
 *
 *     $resource = new Resource();
 *     $mng->addResource($resource, 'type');
 *
 * @author Miguel Ibero <miguel@ibero.me>
 *
 * @see Assetic\Factory\LazyAssetManager;
 * @see Assetic\Factory\Resource\ResourceInterface;
 */
interface ResourceLoaderInterface
{
    /**
     * @param string           $name name of the resource to load
     * @param LazyAssetManager $mng  the manager to be loaded
     */
    public function load($name, LazyAssetManager $mng);
}
