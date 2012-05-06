<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Templating\EngineInterface;

use Meinhof\Model\Page\PageInterface;
use Meinhof\Model\Site\SiteInterface;

class UpdatePagesAction extends OutputAction
{
    protected $site;
    protected $templating;
    protected $output;

    public function __construct(SiteInterface $site, EngineInterface $templating, OutputInterface $output=null)
    {
        $this->site = $site;
        $this->templating = $templating;
        $this->output = $output;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    public function take()
    {
        $pages = $this->site->getPages();
        $globals = $this->site->getGlobals();
        $globals['pages'] = $pages;
        $globals['posts'] = $this->site->getPosts();
        $globals['categories'] = $this->site->getCategories();

        $this->writeOutputLine(sprintf("updating %d pages...", count($pages)), 2);

        foreach($pages as $page){
            if(!$page instanceof PageInterface){
                throw new \RuntimeException("Site returned invalid page.");
            }
            $params = $globals;
            $params['page'] = $page;

            // render page view
            $key = $page->getViewTemplatingKey();
            if(!$this->templating->exists($key)){
                throw new \InvalidArgumentException("View template '${vkey}' does not exist.");
            }
            if(!$this->templating->supports($key)){
                throw new \InvalidArgumentException("View template '${vkey}' does not have a valid format.");
            }            
            $content = $this->templating->render($key, $params);

            $this->site->savePage($page, $content);
            $this->writeOutput(".", 1);
        }
        $this->writeOutputLine("", 1);
        $this->writeOutputLine("done", 2);
    }
}