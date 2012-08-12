<?php

namespace Meinhof\Setup;

/**
 * Read and write the setup of a site
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
interface SetupStoreInterface
{
    /**
     * Writes the setup of a site
     *
     * @param array $params the parameters of the site configuration
     */
    public function write(array $params);

    /**
     * Reads the setup of a site and returns the parameters
     *
     * @return array the current site parameters
     */
    public function read();
}
