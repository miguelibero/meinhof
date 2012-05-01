<?php

namespace Meinhof\Assetic;

interface FormulaLoaderManagerInterface
{
    /**
     * @return array of types
     */
    public function getTypes();

    /**
     * @return Assetic\Factory\Loader\FormulaLoaderInterface
     */
    public function getLoader($type);
}