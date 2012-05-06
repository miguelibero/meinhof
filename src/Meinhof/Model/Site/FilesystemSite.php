<?php

namespace Meinhof\Model\Site;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Templating\EngineInterface;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Post\FilesystemPost;
use Meinhof\Model\Page\PageInterface;
use Meinhof\Model\Page\FilesystemPage;
use Meinhof\Model\Category\CategoryInterface;
use Meinhof\Model\Category\Category;

class FilesystemSite extends AbstractSite
{
    protected $paths = array();
    protected $globals;
    protected $pages;
    protected $categories;
    protected $post_loader;
    protected $post_templating;

    public function __construct(array $paths, array $globals, array $pages, array $categories)
    {
        $this->paths = array_merge($this->paths, $paths);
        $this->globals = $globals;
        $this->setPages($pages);
        $this->setCategories($categories);
    }

    protected function setPages(array $pages)
    {
        $this->pages = array();
        foreach($pages as $page){
            if(is_array($page)){
                $page = FilesystemPage::fromArray($page);
            }
            if(is_string($page)){
                $page = new FilesystemPage($page);
            }
            if(!$page instanceof PageInterface){
                throw new \RuntimeException("Invalid page.");
            }
            if($page instanceof FilesystemPage){
                $page->setSite($this);
            }
            $this->pages[] = $page;
        }
    }

    protected function setCategories(array $categories)
    {
        $this->categories = array();
        foreach($categories as $category){
            if(is_string($category)){
                $category = new Category($category);
            }
            if(!$category instanceof CategoryInterface){
                throw new \RuntimeException("Invalid category.");
            }
            $this->categories[] = $category;
        }
    }    

    public function setPostTemplatingEngine(EngineInterface $engine)
    {
        $this->post_templating = $engine;
    }

    public function setPostMatterLoader(LoaderInterface $loader)
    {
        $this->post_loader = $loader;
    }

    public function getPath($name)
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

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($posts_path);

        $posts = array();
        foreach($finder as $file){
            $path = $file->getRelativePathname();
            $post = FilesystemPost::fromArray(array(
                'key'   => $path,
            ), $this->post_loader);
            if(!$post instanceof PostInterface){
                throw new \RuntimeException("Invalid post.");
            }
            if($post instanceof FilesystemPost){
                $post->setSite($this);
                $post->setTemplatingEngine($this->post_templating);
            }
            $posts[] = $post;
        }
        return $posts;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function getCategories()
    {
        return $this->categories;
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
        return $this->saveFile($post->getSlug(), $content);
    }

    public function savePage(PageInterface $page, $content)
    {
        return $this->saveFile($page->getSlug(), $content);
    }

    protected function saveFile($slug, $content)
    {
        $path = $this->getPath('site').'/'.$slug.'.html';
        file_put_contents($path, $content);   
    }

    public function getGlobals()
    {
        return $this->globals;
    }
}