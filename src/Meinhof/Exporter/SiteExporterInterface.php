<?php

namespace Meinhof\Exporter;

use Meinhof\Model\Site\SiteInterface;
use Meinhof\Model\Post\PostInterface;
use Meinhof\Model\Page\PageInterface;
use Meinhof\Model\Category\CategoryInterface;

/**
 * Exports elements of a site
 */
interface SiteExporterInterface
{
    public function exportPost(PostInterface $post, SiteInterface $site);

    public function exportPage(PageInterface $page, SiteInterface $site);

    public function exportCategory(CategoryInterface $cat, SiteInterface $site);

    public function setParameter($name, $value);
}
