<?php

namespace Meinhof\Templating\Loader;

use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Component\Templating\Storage\Storage;
use Meinhof\Templating\Storage\FrontMatterStorage;

/**
 * This loader implements loading template data
 * with a front configuration like the one used in jekyll
 *
 * https://github.com/mojombo/jekyll/wiki/yaml-front-matter
 */
class FrontMatterLoader implements LoaderInterface
{
    protected $loader;
    protected $matter_separator;

    public function __construct(LoaderInterface $loader, $separator=null)
    {
        $this->loader = $loader;
        $this->matter_separator = $separator;
    }

    public function load(TemplateReferenceInterface $template)
    {
        $storage = $this->loader->load($template);
        if (!$storage instanceof Storage) {
            return false;
        }

        return new FrontMatterStorage($storage, $this->matter_separator);
    }

    /**
     * @{inheritdoc}
     */
    public function isFresh(TemplateReferenceInterface $template, $time)
    {
        return $this->loader->isFresh($template, $time);
    }
}
