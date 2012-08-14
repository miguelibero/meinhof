<?php

namespace Meinhof\Assetic;

/**
 * Returns a list of available resources that may contain
 * asset references for assetic to load.
 */
interface ResourceListerInterface
{
    /**
     * @return array list of strings of available resources
     */
    public function getResources();
}
