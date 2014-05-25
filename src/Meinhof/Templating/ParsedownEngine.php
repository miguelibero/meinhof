<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\Storage\Storage;

/**
 * This engine renders Markdown formatted text using the Parsedown library.
 *
 * @see http://parsedown.org
 * @author Miguel Ibero <miguel@ibero.me>
 */
class ParsedownEngine extends AbstractEngine
{
    protected $parser;

    protected function getName()
    {
        return 'markdown';
    }

    public function setParser(\Parsedown $parser)
    {
        $this->parser = $parser;
    }

    protected function getParser()
    {
        if ($this->parser) {
            return $this->parser;
        }
        return new \Parsedown();
    }

    /**
     * @{inheritdoc}
     */
    protected function parse(Storage $template, array $parameters = array())
    {
        $parser = $this->getParser();

        return $parser->text($template->getContent());
    }
}
