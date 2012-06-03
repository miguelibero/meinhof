<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\Storage\Storage;

use dflydev\markdown\MarkdownExtraParser;

/**
 * This engine renders Markdown formatted text.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class MarkdownEngine extends AbstractEngine
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
