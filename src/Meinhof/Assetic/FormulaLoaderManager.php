<?php

namespace Meinhof\Assetic;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;

class FormulaLoaderManager implements FormulaLoaderManagerInterface
{
    protected $loaders = array();

    public function __construct(array $loaders=array())
    {
        $this->loaders = $loaders;
    }

    public function setLoader($type, FormulaLoaderInterface $loader)
    {
        $this->loaders[$type] = $loader;
    }

    public function getLoader($type)
    {
        if(!isset($this->loaders[$type])){
            throw new \InvalidArgumentException("No formula loader defined for type '${type}'.");
        }
        return $this->loaders[$type];
    }

    public function getTypes()
    {
        return array_keys($this->loaders);
    }
}