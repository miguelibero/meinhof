<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\Storage\Storage;

use dflydev\markdown\MarkdownExtraParser;

class MarkdownEngine extends Engine
{
    protected function getName()
    {
        return 'markdown';
    }

    /**
     * @{inheritdoc}
     */
    protected function parse(Storage $template, array $parameters = array())
    {
        $parser = new MarkdownExtraParser();
        return $parser->transformMarkdown($template->getContent());
    } 
}