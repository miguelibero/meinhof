<?php

namespace Meinhof\Site;

use Symfony\Component\Finder\Finder;

use Meinhof\Post\PostInterface;
use Meinhof\Post\FilesystemPost;
use Symfony\Component\Config\Loader\LoaderInterface;

class FilesystemSite extends AbstractSite
{
    protected $paths = array();
    protected $globals;
    protected $post_loader;

    public function __construct(array $paths, array $globals)
    {
        $this->paths = array_merge($this->paths, $paths);
        $this->globals = $globals;
    }

    public function setPostMatterLoader(LoaderInterface $loader)
    {
        $this->post_loader = $loader;
    }

    protected function getPath($name)
    {
        if(!is_array($this->paths) || !isset($this->paths[$name])){
            throw new \InvalidArgumentException("Could not find path ${name}.");
        }
        $path = $this->paths[$name];
        if(substr($path,0,1) !== '/'){
            if(!isset($this->paths['base'])){
                throw new \InvalidArgumentException('No base path defined.');
            }
            $path = $this->paths['base'].'/'.$path;
        }
        return $path;
    }

    public function getPosts()
    {
        $posts_path = $this->getPath('posts');
        $layouts_path = $this->getPath('views');
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($posts_path);
        $posts = array();
        foreach($finder as $file){
            $path = $file->getRealPath();
            if(substr($path, 0, strlen($posts_path))){
                $path = substr($path, strlen($posts_path));
                $path = trim($path, '/');
            }
            $posts[] = FilesystemPost::fromArray(array(
                'key'   => $path,
                'paths' => $this->paths,
            ), $this->post_loader);
        }
        return $posts;
    }

    public function getViews()
    {
        // find all templates
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($this->getPath('views'));
        $paths = array();
        foreach($finder as $file){
            $paths[] = $file->getRelativePathname();
        }
        return $paths;
    }

    public function savePost(PostInterface $post, $content)
    {
        $path = $this->getPath('site').'/'.$post->getSlug().'.html';
        file_put_contents($path, $content);
    }

    public function getGlobals()
    {
        return $this->globals;
    }
}