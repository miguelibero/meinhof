<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Templating\EngineInterface;

use Meinhof\Helper\UrlHelperInterface;
use Meinhof\Model\Page\PageInterface;
use Meinhof\Model\Site\SiteInterface;
use Meinhof\Exporter\ExporterInterface;

class UpdatePagesAction extends OutputAction
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
        $pages = $this->site->getPages();
        $this->writeOutputLine(sprintf("updating %d pages...", count($pages)), 2);

        foreach($pages as $page){
            if(!$page instanceof PageInterface){
                throw new \RuntimeException("Site returned invalid page.");
            }
            $this->exporter->exportPage($page, $this->site);
            $this->writeOutput(".", 1);
        }
        $this->writeOutputLine("", 1);
        $this->writeOutputLine("done", 2);
    }
}