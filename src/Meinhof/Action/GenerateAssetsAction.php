<?php

namespace Meinhof\Action;

use Assetic\AssetWriter;
use Assetic\Factory\Resource\ResourceInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\Factory\Loader\FormulaLoaderInterface;

use Meinhof\Site\SiteInterface;
use Meinhof\Assetic\ResourceLoaderInterface;

class GenerateAssetsAction implements ActionInterface
{
    protected $site;
    protected $factory;
    protected $writer;
    protected $resource_loader;
    protected $formula_loaders = array();

    public function __construct(SiteInterface $site, AssetFactory $factory, AssetWriter $writer,
        ResourceLoaderInterface $resource_loader, array $formula_loaders=array())
    {
        $this->site = $site;
        $this->factory = $factory;
        $this->writer = $writer;
        $this->resource_loader = $resource_loader;
        $this->formula_loaders = $formula_loaders;
    }

    public function setFormulaLoader($type, FormulaLoaderInterface $loader)
    {
        $this->formula_loaders[$type] = $loader;
    }

    public function getEventName()
    {
        return 'generate';
    }

    public function getName()
    {
        return 'assets';
    }

    public function take()
    {
        $manager = new LazyAssetManager($this->factory);

        // load formula loaders, done lazily to avoid circular dependencies
        foreach($this->formula_loaders as $type=>$loader){
            $manager->setLoader($type, $loader);
        }

        // load template resources
        foreach($this->site->getViews() as $view){
            $this->resource_loader->load($view, $manager);
        }

        // write output assets
        $this->writer->writeManagerAssets($manager);
    }
}