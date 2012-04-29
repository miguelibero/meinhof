<?php

namespace Meinhof\Configuration;

use Symfony\Component\Finder\Finder;

class FilesystemConfiguration implements ConfigurationInterface
{
    protected $base_dir;
    protected $paths = array();
    protected $globals;

    public function __construct($base_dir, array $paths, array $globals)
    {
        $this->base_dir = $base_dir;
        $this->paths = array_merge($this->paths, $paths);
        $this->globals = $globals;
    }

    protected function getPath($name)
    {
        if(!is_array($this->paths) || !isset($this->paths[$name])){
            throw new \InvalidArgumentException("Could not find path ${name}.");
        }
        $path = $this->paths[$name];
        if(substr($path,0,1) !== '/'){
            $path = $this->base_dir.'/'.$path;
        }
        return $path;
    }

    public function getPosts()
    {
        // find all posts
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($this->getPath('posts'));
        $paths = array();
        foreach($finder as $file){
            $paths[] = $file->getRelativePathname();
        }
        return $paths;
    }

    public function getLayoutForPost($post)
    {
        $finder = new Finder();
        $finder->files()
            ->name('post.*')
            ->ignoreVCS(true)
            ->in($this->getPath('views'));
        foreach($finder as $file){
            return $file->getRelativePathname();
        }
    }

    public function savePost($post, $content)
    {
        $parts = explode('.', $post);
        $post = reset($parts);
        $path = $this->getPath('site').'/'.$post.'.html';
        file_put_contents($path, $content);
    }

    public function getGlobals()
    {
        return $this->globals;
    }
}