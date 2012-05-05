<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meinhof\Templating;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Symfony\Component\Templating\TemplateReference;

class FixedTemplateNameParser implements TemplateNameParserInterface
{
    protected $engine;

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
