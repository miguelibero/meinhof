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
        if(substr($skeleton,0,1) !== '/' && !parse_url($skeleton, PHP_URL_SCHEME)){
            $skeleton = __DIR__.'/../'.$skeleton;
        }
        if(!is_readable($skeleton) || !is_dir($skeleton)){
            throw new \RuntimeException("Skeleton path '${skeleton}' is not a readable directory.");
        }        
        return realpath($skeleton);        
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
        foreach($finder as $file){
            $path = $file->getRealPath();
            if(mb_substr($path,0,$k) === $this->skeleton."/"){
                $path = mb_substr($path, $k);
            }
            $paths[] = $path;
        }
        return $paths;
    }

    protected function saveFile($path, $content)
    {
        $dir = dirname($path);
        if(is_file($dir)){
            throw new \RuntimeException("Could not use directory '${dir}' since it is a file.");
        }
        if(!file_exists($dir) && @mkdir($dir, 0755, true) === false){
            throw new \RuntimeException("Could not create directory '${dir}'.");
        }
        if(@file_put_contents($path, $content) === false){
            throw new \RuntimeException("Could not create File '${path}'.");   
        }
    }

    public function generate(array $params, $dir)
    {
        foreach($this->getSkeletonFiles() as $file){
            $content = $this->render($file, $params);
            $path = $dir.'/'.$file;
            $this->saveFile($path, $content);
        }
    }
}