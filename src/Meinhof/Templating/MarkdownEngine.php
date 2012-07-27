<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\Storage\Storage;

use dflydev\markdown\MarkdownExtraParser;
use dflydev\markdown\IMarkdownParser;

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

    public function setParser(IMarkdownParser $parser)
    {
        $this->parser = $parser;
    }

    protected function getParser()
    {
        if($this->parser){
            return $this->parser;
        }
        return new MarkdownExtraParser();
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
