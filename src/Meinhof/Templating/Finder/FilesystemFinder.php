<?php

namespace Meinhof\Templating\Finder;

use Symfony\Component\Finder\Finder;

class FilesystemFinder implements FinderInterface
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function find($pattern)
    {
        $finder = new Finder();
        $finder->files()
            ->name($pattern.'.*')
            ->ignoreVCS(true)
            ->in($this->path);

        foreach ($finder as $file) {
            return $file->getRelativePathname();
        }
    }
}
