<?php

namespace Meinhof\Templating\Twig\Extension;

use Meinhof\Helper\UrlHelperInterface;

use Meinhof\Export\ExportEvent;

class UrlExtension extends \Twig_Extension
{
    protected $helper;
    protected $relativeRoot;
    protected $parameters;

    public function __construct(UrlHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function onExport(ExportEvent $event)
    {
        $this->relativeRoot = $event->getRelativeRoot();
        $this->parameters = $event->getParameters();
    }

    public function getName()
    {
        return 'url';
    }

    public function getFunctions()
    {
        return array(
            'url'   => new \Twig_Function_Method($this, "getUrl")
        );
    }

    public function getUrl($obj, array $params=array(), $absolute=false)
    {
        if (!$obj) {
            return "";
        }
        if (is_array($this->parameters)) {
            $params = array_merge($this->parameters, $params);
        }
        if (is_object($obj)) {
            $obj = $this->helper->getUrl($obj, $params);
        }
        if (is_string($obj)) {
            return $absolute ? $obj : $this->relativeRoot.$obj ;
        }
        throw new \InvalidArgumentException("Could not get the url.");
    }

}
