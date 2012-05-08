<?php

namespace Meinhof\Templating\Twig;

use Meinhof\Helper\UrlHelperInterface;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

class UrlExtension extends \Twig_Extension
{
    protected $helper;

    public function __construct(UrlHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function getName()
    {
        return 'url';
    }

    public function getFunctions()
    {
        return array(
            'url' => new \Twig_Function_Method($this, "getUrl"),
        );
    }

    public function getUrl($obj)
    {
        if($obj instanceof PostInterface){
            return $this->helper->getPostUrl($obj);
        }
        if($obj instanceof PageInterface){
            return $this->helper->getPageUrl($obj);
        }        
        throw new \InvalidArgumentException("Do not know the parameter.");
    }
}