<?php

namespace Meinhof\Templating\Twig\Extension;

use Meinhof\Helper\UrlHelperInterface;

use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;
use Meinhof\Model\Category\CategoryInterface;
use Meinhof\Exporter\ExportEvent;

class UrlExtension extends \Twig_Extension
{
    protected $helper;
    protected $webroot;

    public function __construct(UrlHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function beforeExport(ExportEvent $event)
    {
        $this->webroot = $event->getRelativeRoot();
    }

    public function afterExport(ExportEvent $event)
    {
        $this->webroot = '';
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

    public function getUrl($obj)
    {
        if (!$obj) {
            return "";
        }
        if ($obj instanceof PostInterface) {
            $obj = $this->helper->getPostUrl($obj);
        }
        if ($obj instanceof PageInterface) {
            $obj = $this->helper->getPageUrl($obj);
        }
        if ($obj instanceof CategoryInterface) {
            $obj = $this->helper->getCategoryUrl($obj);
        }        
        if (is_string($obj)) {
            return $this->webroot.$obj;
        }
        throw new \InvalidArgumentException("Do not know the parameter.");
    }

}
