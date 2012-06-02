<?php

namespace Meinhof\Assetic;

use Assetic\Factory\Loader\FormulaLoaderInterface;

/**
 * This class manages a list of formula loaders,
 * each one having a different type.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class FormulaLoaderManager implements FormulaLoaderManagerInterface
{
    protected $loaders = array();

    /**
     * @param array $loaders initial list of loaders, the type should be the array key
     */
    public function __construct(array $loaders=array())
    {
        $this->setLoaders($loaders);
    }

    /**
     * Adds a list of loaders
     *
     * @param array $loaders list of loaders, the type should be the array key
     */
    protected function setLoaders(array $loaders)
    {
        $this->loaders = array();
        foreach ($loaders as $type=>$loader) {
            if (!$loader instanceof FormulaLoaderInterface) {
                throw new \InvalidArgumentException("Not a valid formula loader with key '${type}'.");
            }
            $this->setLoader($type, $loader);
        }
    }

    /**
     * Adds a formula loader
     *
     * @param string                 $type   the formula type
     * @param FormulaLoaderInterface $loader the formula loader
     */
    public function setLoader($type, FormulaLoaderInterface $loader)
    {
        $this->loaders[$type] = $loader;
    }

    /**
     * Returns the formula loader specified for a given type
     *
     * @throws \InvalidArgumentException if no loader defined for the given type
     * @param  string                    $type the formula type
     */
    public function getLoader($type)
    {
        if (!isset($this->loaders[$type])) {
            throw new \InvalidArgumentException("No formula loader defined for type '${type}'.");
        }

        return $this->loaders[$type];
    }

    /**
     * Returns a list of all defined formula types
     *
     * @return array array of strings of the type names
     */
    public function getTypes()
    {
        return array_keys($this->loaders);
    }
}
