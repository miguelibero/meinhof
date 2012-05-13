<?php

namespace Meinhof\Templating\Twig\Extension;

use Symfony\Component\Templating\EngineInterface;

class ContentExtension extends \Twig_Extension
{
    protected $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function getName()
    {
        return 'content';
    }

    public function getFunctions()
    {
        return array(
            'content'   => new \Twig_Function_Method($this, "getContent")
        );
    }

    public function getContent($key, array $params=array())
    {
        return $this->engine->render($key, $params);
    }

}