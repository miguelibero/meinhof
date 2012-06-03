<?php

namespace Meinhof\Templating;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Component\Templating\TemplateReference;

/**
 * This name parser always returns templates with the same engine.
 * This is useful when rendering inline templates that to not have names.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class FixedTemplateNameParser implements TemplateNameParserInterface
{
    protected $engine;

    /**
     * @param mixed $engine the fixed engine that will be passed to all templates
     */
    public function __construct($engine)
    {
        $this->engine = $engine;
    }

    /**
     * @inheritDoc
     */
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        return new TemplateReference($name, $this->engine);
    }
}
