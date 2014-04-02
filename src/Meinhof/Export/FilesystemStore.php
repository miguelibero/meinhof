<?php

namespace Meinhof\Export;

class FilesystemStore implements StoreInterface
{
    protected $base_path;

    public function __construct($base_path)
    {
        $this->base_path = $base_path;
    }

    public function store($url, $content)
    {
        $path = $this->base_path.DIRECTORY_SEPARATOR .trim($url,DIRECTORY_SEPARATOR );
        @mkdir(dirname($path), 0755, true);
        if (@file_put_contents($path, $content) === false) {
            throw new \RuntimeException("Could not save url '${url}'.");
        }
    }
}
