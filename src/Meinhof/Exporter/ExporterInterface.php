<?php

namespace Meinhof\Exporter;

use Meinhof\Model\Site\SiteInterface;
use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;

/**
 * A way to store a template.
 */
interface ExporterInterface
{
    public function exportPost(PostInterface $post, SiteInterface $site);
    
    public function exportPage(PageInterface $page, SiteInterface $site);
}