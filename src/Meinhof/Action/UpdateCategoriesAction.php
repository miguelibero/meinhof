<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Model\Category\CategoryInterface;
use Meinhof\Model\Site\SiteInterface;
use Meinhof\Exporter\SiteExporterInterface;

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
        SiteExporterInterface $exporter, OutputInterface $output=null)
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
        $cats = $this->site->getCategories();
        $this->writeOutputLine(sprintf("updating %d categories...", count($cats)), 2);

        foreach ($cats as $cat) {
            if (!$cat instanceof CategoryInterface) {
                throw new \RuntimeException("Site returned invalid category.");
            }
            $this->exporter->exportCategory($cat, $this->site);
            $this->writeOutput(".", 1);
        }
        $this->writeOutputLine("", 1);
        $this->writeOutputLine("done", 2);
    }
}
