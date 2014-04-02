<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\Storage\Storage;

use Michelf\MarkdownExtra;
use Michelf\MarkdownInterface;

/**
 * This engine renders Markdown formatted text.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class MarkdownEngine extends AbstractEngine
{
    protected $parser;

    protected function getName()
    {
        return 'markdown';
    }

    public function setParser(MarkdownInterface $parser)
    {
        $this->parser = $parser;
    }

    protected function getParser()
    {
        if ($this->parser) {
            return $this->parser;
        }

        return new MarkdownExtra();
    }

    /**
     * @{inheritdoc}
     */
    protected function parse(Storage $template, array $parameters = array())
    {
        $parser = $this->getParser();

        return $parser->transformMarkdown($template->getContent());
    }
}
