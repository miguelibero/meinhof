<?php

namespace Meinhof\Assetic;

/**
 * Defines a manager for formula loaders
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
interface FormulaLoaderManagerInterface
{
    /**
     * Returns a list of accepted types
     *
     * @return array list of types
     */
    public function getTypes();

    /**
     * Returns a formula loader for a given type
     *
     * @param string $type the formula type
     *
     * @return Assetic\Factory\Loader\FormulaLoaderInterface
     */
    public function getLoader($type);
}
