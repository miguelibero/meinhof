<?php

namespace Meinhof\Generator;

use Symfony\Component\Templating\EngineInterface;

class TemplatingEngineGenerator extends SkeletonGenerator
{
    protected $engine;

    public function __construct($skeleton, EngineInterface $engine)
    {
        parent::__construct($skeleton);
        $this->engine = $engine;
    }

    protected function render($name, array $params)
    {
        return $this->engine->render($name, $params);
    }
}
