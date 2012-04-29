<?php

namespace Meinhof\Config\Loader;

use Symfony\Component\Templating\Loader\LoaderInterface as TemplateLoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Config\Loader\LoaderInterface as ConfigLoaderInterface;
use Symfony\Component\Config\Loader\Loader as ConfigLoader;
use Meinhof\Templating\Storage\MatterStorage;

/**
 * This loader tries to read matter from a template loader
 * that returns MatterStorage elements. These elements
 * are then passed to a config loader to obtain the configuration array.
 */
class TemplateMatterLoader extends ConfigLoader
{
    protected $template_loader;
    protected $config_loader;
    protected $parser;
    protected $cache = array();

    public function __construct(
        TemplateNameParserInterface $parser,
        TemplateLoaderInterface $template_loader,
        ConfigLoaderInterface $config_loader)
    {
        $this->parser = $parser;
        $this->template_loader = $template_loader;
        $this->config_loader = $config_loader;
    }

    protected function getConfigLoader()
    {
        return $this->config_loader;
    }

    public function load($resource, $type = null)
    {
        $storage = $this->loadStorage($resource);
        return $this->config_loader->load($storage);
    }

    public function supports($resource, $type = null)
    {
        try{
            $storage = $this->loadStorage($resource);
            return $this->config_loader->supports($storage);
        }catch(\Exception $e){
            return false;
        }
    }

    public function loadStorage($resource)
    {
        $template = $this->parser->parse($resource);

        $key = $template->getLogicalName();
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $storage = $this->template_loader->load($template);
        if (!$storage instanceof MatterStorage) {
            throw new \InvalidArgumentException(sprintf('The template "%s" does not have matter.', $template));
        }

        return $this->cache[$key] = $storage;
    }
}