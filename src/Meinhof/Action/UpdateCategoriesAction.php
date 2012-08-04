<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Model\Category\CategoryInterface;
use Meinhof\Model\Site\SiteInterface;
use Meinhof\Export\ExporterInterface;

/**
 * This action calls the exporter on all the categories.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class UpdateCategoriesAction extends OutputAction
{
    protected $site;
    protected $exporter;
    protected $output;

    public function __construct(SiteInterface $site,
        ExporterInterface $exporter, OutputInterface $output=null)
    {
        $this->site = $site;
        $this->exporter = $exporter;
        $this->output = $output;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    public function take()
    {
        $categories = $this->site->getCategories();
        $this->writeOutputLine(sprintf("updating %d categories...", count($categories)), 2);

        foreach ($categories as $category) {
            if (!$category instanceof CategoryInterface) {
                throw new \RuntimeException("Site returned invalid category.");
            }
            $params = array(
                'category'  => $category,
                'site'  => $this->site
            );
            $this->exporter->export($category, $category->getViewTemplatingKey(), $params);
            $this->writeOutput(".", 1);
        }
        $this->writeOutputLine("", 1);
        $this->writeOutputLine("done", 2);
    }
}
