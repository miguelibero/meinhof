<?php

namespace Meinhof\Config\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Yaml\Yaml;

use Meinhof\Templating\Storage\MatterStorage;

/**
 * This loader parses a MatterStorage matter as yaml.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 *
 * @see Meinhof\Templating\Storage\MatterStorage
 */
class YamlMatterLoader extends Loader
{
    /**
     * {@inheritDoc}
     */
    public function load($resource, $type = null)
    {
        if (!$resource instanceof MatterStorage) {
            throw new \InvalidArgumentException("Only accepts MatterStorage resources.");
        }
        $matter = $resource->getMatter();

        return Yaml::parse($matter);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        return $resource instanceof MatterStorage;
    }
}
