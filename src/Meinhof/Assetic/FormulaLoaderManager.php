<?php

namespace Meinhof\Assetic;

use Assetic\Factory\Loader\FormulaLoaderInterface;

class FormulaLoaderManager implements FormulaLoaderManagerInterface
{
    protected $loaders = array();

    public function __construct(array $loaders=array())
    {
        $this->setLoaders($loaders);
    }

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

    public function setLoader($type, FormulaLoaderInterface $loader)
    {
        $this->loaders[$type] = $loader;
    }

    public function getLoader($type)
    {
        if (!isset($this->loaders[$type])) {
            throw new \InvalidArgumentException("No formula loader defined for type '${type}'.");
        }

        return $this->loaders[$type];
    }

    public function getTypes()
    {
        return array_keys($this->loaders);
    }
}
