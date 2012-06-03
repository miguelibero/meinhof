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
 *
 * @author Miguel Ibero <miguel@ibero.me>
 *
 * @see Meinhof\Templating\Storage\MatterStorage
 */
class TemplateMatterLoader extends ConfigLoader
{
    protected $template_loader;
    protected $config_loader;
    protected $parser;
    protected $cache = array();
    protected $defaults = array();

    /**
     * @param TemplateNameParserInterface $parser          parses the template name to get the logical name
     * @param TemplateLoaderInterface     $template_loader loads the template and should return a matter storage
     * @param ConfigLoaderInterface       $config_loader   the real configuration loader that will read the matter
     * @param array|null                  $defaults        the default values
     */
    public function __construct(
        TemplateNameParserInterface $parser,
        TemplateLoaderInterface $template_loader,
        ConfigLoaderInterface $config_loader,
        $defaults = null)
    {
        $this->parser = $parser;
        $this->template_loader = $template_loader;
        $this->config_loader = $config_loader;
        if (is_array($defaults)) {
            $this->defaults = $defaults;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function load($resource, $type = null)
    {
        $storage = $this->loadStorage($resource);
        $data = $this->config_loader->load($storage);
        if (!is_array($data)) {
            throw new \RuntimeException("Config loader returned invalid data");
        }

        return array_merge($data, $this->defaults);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        try {
            $storage = $this->loadStorage($resource);

            return $this->config_loader->supports($storage);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Loads a resource from the template loader
     *
     * @param string $resource the name of the resource to load
     *
     * @throws \RuntimeException if the template loader does not return a MatterStorage
     */
    protected function loadStorage($resource)
    {
        $template = $this->parser->parse($resource);

        $key = $template->getLogicalName();
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $storage = $this->template_loader->load($template);
        if (!$storage instanceof MatterStorage) {
            throw new \RuntimeException(sprintf('The template "%s" does not have matter.', $template));
        }

        return $this->cache[$key] = $storage;
    }
}
