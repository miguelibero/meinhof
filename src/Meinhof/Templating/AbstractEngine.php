<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

abstract class AbstractEngine implements EngineInterface
{
    protected $loader;
    protected $parser;
    protected $cache = array();

    /**
     * Constructor.
     *
     * @param TemplateNameParserInterface $parser  A TemplateNameParserInterface instance
     * @param LoaderInterface             $loader  A loader instance
     */
    public function __construct(TemplateNameParserInterface $parser, LoaderInterface $loader)
    {
        $this->parser  = $parser;
        $this->loader  = $loader;
    }

    /**
     * Renders a template.
     *
     * @param mixed $name       A template name or a TemplateReferenceInterface instance
     * @param array $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \InvalidArgumentException if the template does not exist
     * @throws \RuntimeException         if the template cannot be rendered
     *
     * @api
     */
    public function render($name, array $parameters = array())
    {
        $storage = $this->load($name);
        $key = md5(serialize($storage));
        $this->current = $key;

        // render
        if (!$storage instanceof Storage || false === $content = $this->parse($storage, $parameters)) {
            throw new \RuntimeException(sprintf('The template "%s" cannot be rendered.', $name));
        }

        return $content;
    }

    /**
     * Returns true if the template exists.
     *
     * @param mixed $name A template name or a TemplateReferenceInterface instance
     *
     * @return Boolean true if the template exists, false otherwise
     *
     * @api
     */
    public function exists($name)
    {
        try {
            $this->load($name);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Returns true if this class is able to render the given template.
     *
     * @param mixed $name A template name or a TemplateReferenceInterface instance
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     *
     * @api
     */
    public function supports($name)
    {
        $template = $this->parser->parse($name);

        return $this->getName() === $template->get('engine');
    }

    /**
     * Evaluates a template.
     *
     * @param Storage $template   The template to render
     * @param array   $parameters An array of parameters to pass to the template
     *
     * @return string|false The evaluated template, or false if the engine is unable to render the template
     */
    abstract protected function parse(Storage $template, array $parameters = array());

    abstract protected function getName();

    /**
     * Loads the given template.
     *
     * @param mixed $name A template name or a TemplateReferenceInterface instance
     *
     * @return Storage A Storage instance
     *
     * @throws \InvalidArgumentException if the template cannot be found
     */
    protected function load($name)
    {
        $template = $this->parser->parse($name);

        if(!$template instanceof TemplateReferenceInterface){
            throw new \RuntimeException(sprintf('The template "%s" could not be parsed.', $name));
        }

        $key = $template->getLogicalName();
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $storage = $this->loader->load($template);

        if (!$storage instanceof Storage) {
            throw new \RuntimeException(sprintf('The template "%s" could not be loaded.', $name));
        }

        return $this->cache[$key] = $storage;
    }
}