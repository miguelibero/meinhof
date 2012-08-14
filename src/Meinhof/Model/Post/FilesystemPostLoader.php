<?php

namespace Meinhof\Model\Post;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Config\Loader\LoaderInterface as ConfigLoaderInterface;
use Symfony\Component\Config\Definition\Processor as ConfigProcessor;

use Meinhof\DependencyInjection\PostMatterConfiguration;
use Meinhof\Templating\Finder\FinderInterface;

class FilesystemPostLoader extends PostLoader
{
    protected $path;
    protected $templating;
    protected $configLoader;

    public function __construct(EngineInterface $templating,
        $path, ConfigLoaderInterface $loader, FinderInterface $finder=null)
    {
        $this->path = $path;
        $this->configLoader = $loader;
        $posts = $this->loadFilesystemPosts();
        parent::__construct($posts, $templating, $finder);
    }

    protected function loadMatter($key)
    {
        if (!$this->configLoader || !$this->configLoader->supports($key)) {
            return array();
        }
        $matter = $this->configLoader->load($key);
        if (!is_array($matter)) {
            return array();
        }
        $matter = array('post' => $matter);
        $processor = new ConfigProcessor();
        $configuration = new PostMatterConfiguration();

        return $processor->processConfiguration($configuration, $matter);
    }

    public function loadFilesystemPosts()
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($this->path);

        $posts = array();
        foreach ($finder as $file) {
            $path = $file->getRelativePathname();
            $config = $this->loadMatter($path);
            if (!isset($config['key'])) {
                $parts = explode('.', $path);
                $config['key'] = reset($parts);
            }
            $posts[] = $config;
        }

        return $posts;
    }

    protected function createPost($data)
    {
        if (is_array($data)) {
            if (!isset($data['updated']) && isset($data['key'])) {
                $data['updated'] = $this->getKeyPathUpdated($data['key']);
            }
        }

        return parent::createPost($data);
    }

    protected function getKeyPathUpdated($key)
    {
        $path = $this->path.'/'.$key;
        if (!is_readable($path)) {
            return null;
        }
        $timestamp = filemtime($path);
        $date = new \DateTime();
        $date->setTimestamp($timestamp);

        return $date;
    }
}
