<?php

namespace Meinhof\Exporter;

use Meinhof\Model\Site\SiteInterface;
use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

use Meinhof\Helper\UrlHelperInterface;

/**
 * Exports each element for a list of locales
 */
class LocalizedExporter implements ExporterInterface
{
    protected $locales;
    protected $exporter;
    protected $url_helper;

    public function __construct(array $locales, ExporterInterface $exporter, UrlHelperInterface $url_helper)
    {
        $this->locales = $locales;
        $this->exporter = $exporter;
        $this->url_helper = $url_helper;
    }

    public function exportPost(PostInterface $post, SiteInterface $site)
    {
        foreach($this->locales as $locale){
            $this->url_helper->setParameter('locale', $locale);
            $this->exporter->exportPost($post, $site);
        }
    }
    
    public function exportPage(PageInterface $page, SiteInterface $site)
    {
        foreach($this->locales as $locale){
            $this->url_helper->setParameter('locale', $locale);
            $this->exporter->exportPage($page, $site);
        }
    }
}