<?php

namespace Meinhof\Action;

use Assetic\AssetWriter;
use Assetic\Factory\Resource\ResourceInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Loader\FormulaLoaderInterface;

use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Site\SiteInterface;
use Meinhof\Assetic\ResourceLoaderInterface;
use Meinhof\Assetic\FormulaLoaderManagerInterface;

class UpdateAssetsAction extends OutputAction
{
    protected $site;
    protected $factory;
    protected $writer;
    protected $resource_loader;
    protected $formula_loader;
    protected $output;

    public function __construct(SiteInterface $site, AssetFactory $factory,
        AssetWriter $writer, ResourceLoaderInterface $resource_loader,
        FormulaLoaderManagerInterface $formula_loader_manager, OutputInterface $output=null)
    {
        $this->site = $site;
        $this->factory = $factory;
        $this->writer = $writer;
        $this->resource_loader = $resource_loader;
        $this->formula_loader_manager = $formula_loader_manager;
        $this->output = $output;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    public function take()
    {
        $this->writeOutputLine("updating assets...", 2);

        $manager = new LazyAssetManager($this->factory);

        // load formula loaders, done lazily to avoid circular dependencies
        foreach($this->formula_loader_manager->getTypes() as $type){
            $manager->setLoader($type, $this->formula_loader_manager->getLoader($type));
        }

        // load template resources
        foreach($this->site->getViews() as $view){
            $this->resource_loader->load($view, $manager);
        }

        // write output assets
        $this->writer->writeManagerAssets($manager);

        $this->writeOutputLine("done", 2);
    }
}