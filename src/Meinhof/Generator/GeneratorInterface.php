<?php

namespace Meinhof\Generator;

/**
 * Generate a file structure
 */
interface GeneratorInterface
{
    /**
     * Generate the file structure somewhere
     *
     * @param array  $params parameters to be substituted in the generator
     * @param string $path   output path where the generated files will be written
     */
    public function generate(array $params, $path);
}
