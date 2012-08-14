<?php

namespace Meinhof\Assetic;

use Symfony\Component\Finder\Finder;

/**
 * This resource lister returns all the files in a given path.
 */
class FilesystemResourceLister implements ResourceListerInterface
{
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function getResources()
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($this->path);
        $paths = array();
        foreach ($finder as $file) {
            $paths[] = $file->getRelativePathname();
        }

        return $paths;
    }
}
