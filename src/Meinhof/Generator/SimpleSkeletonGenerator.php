<?php

namespace Meinhof\Generator;

use Symfony\Component\Templating\TemplateNameParser;
use Meinhof\Templating\SimpleEngine;
use Meinhof\Templating\FixedTemplateNameParser;
use Meinhof\Templating\Loader\FilesystemLoader;

class SimpleSkeletonGenerator extends TemplatingEngineGenerator
{
    protected $engine;

    public function __construct($skeleton)
    {
        $skeleton = $this->fixSkeletonPath($skeleton);
        $parser = new FixedTemplateNameParser('simple');
        $loader = new FilesystemLoader(array($skeleton));
        $engine = new SimpleEngine($parser, $loader);
        
        parent::__construct($skeleton, $engine);
    }

}