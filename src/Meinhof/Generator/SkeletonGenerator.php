<?php

namespace Meinhof\Generator;

use Symfony\Component\Finder\Finder;

abstract class SkeletonGenerator implements GeneratorInterface
{
    protected $skeleton;

    public function __construct($skeleton)
    {
        $this->skeleton = $this->fixSkeletonPath($skeleton);
    }

    abstract protected function render($name, array $params);

    protected function fixSkeletonPath($skeleton)
    {
        if (!$this->isAbsolutePath($skeleton)) {
            $skeleton = __DIR__.'/../'.$skeleton;
        }
        if (!is_readable($skeleton) || !is_dir($skeleton)) {
            throw new \RuntimeException("Skeleton path '${skeleton}' is not a readable directory.");
        }

        return $skeleton;
    }

    private function isAbsolutePath($file)
    {
        $protocol = strpos($file, "://");
        $file = substr($file, $protocol + ($protocol ? 3 : 0));

        if ($file[0] == '/' || $file[0] == '\\'
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && $file[1] == ':'
                && ($file[2] == '\\' || $file[2] == '/')
            )
        ) {
            return true;
        }

        return false;
    }

    protected function getSkeletonPath()
    {
        return $this->skeleton;
    }

    protected function getSkeletonFiles()
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($this->skeleton);

        $paths = array();

        $k = mb_strlen($this->skeleton)+1;
        foreach ($finder as $file) {
            $paths[] = $file->getRelativePathname();
        }

        return $paths;
    }

    protected function saveFile($path, $content)
    {
        $dir = dirname($path);
        if (is_file($dir)) {
            throw new \RuntimeException("Could not use directory '${dir}' since it is a file.");
        }
        if (!file_exists($dir) && @mkdir($dir, 0755, true) === false) {
            throw new \RuntimeException("Could not create directory '${dir}'.");
        }
        if (@file_put_contents($path, $content) === false) {
            throw new \RuntimeException("Could not create File '${path}'.");
        }
    }

    public function generate(array $params, $dir)
    {
        foreach ($this->getSkeletonFiles() as $file) {
            $content = $this->render($file, $params);
            $path = $dir.'/'.$file;
            $this->saveFile($path, $content);
        }
    }
}
