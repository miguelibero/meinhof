<?php

namespace Meinhof\Action;

use Assetic\AssetWriter;
use Assetic\Factory\LazyAssetManager;

use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Model\Site\SiteInterface;
use Meinhof\Assetic\ResourceLoaderInterface;
use Meinhof\Assetic\FormulaLoaderManagerInterface;

/**
 * Compiles the site assets.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class UpdateAssetsAction extends OutputAction
{
    protected $site;
    protected $manager;
    protected $writer;
    protected $resource_loader;
    protected $formula_loader;
    protected $output;
    protected $assets = array();

    public function __construct(SiteInterface $site, LazyAssetManager $manager,
        AssetWriter $writer, ResourceLoaderInterface $resource_loader,
        FormulaLoaderManagerInterface $formula_loader_manager,
        OutputInterface $output=null)
    {
        $this->site = $site;
        $this->manager = $manager;
        $this->writer = $writer;
        $this->resource_loader = $resource_loader;
        $this->formula_loader_manager = $formula_loader_manager;
        $this->output = $output;
    }

    /**
     * Adds a list of assets to be processed by the asset writer.
     *
     * @param array assets the asset list
     */
    public function addAssets(array $assets)
    {
        foreach ($assets as $asset) {
            if (!is_array($asset)) {
                throw new \InvalidArgumentException('Each element needs to be an array.');
            }
            $this->addAsset($asset);
        }
    }

    /**
     * Adds an asset to be processed by the asset writer.
     *
     * Accepted keys in the asset array are:
     * * `name`: the name of the asset
     * * `input`: the input assets (required)
     * * `filter`: the assetic filters to be applied (by default)
     * * `output`: the output path (by default the same as input)
     * * `options`: other asset options
     *
     * @param array asset the asset configuration
     */
    public function addAsset(array $asset)
    {
        if (!isset($asset['input'])) {
            throw new \InvalidArgumentException('Asset needs an input.');
        }
        if (!isset($asset['name'])) {
            $asset['name'] = uniqid();
        }
        if (!isset($asset['filter'])) {
            $asset['filter'] = array();
        }
        if (!is_array($asset['filter'])) {
            $asset['filter'] = explode(',', $asset['filter']);
        }
        if (!isset($asset['options']) || !is_array($asset['options'])) {
            $asset['options'] = array();
        }
        if (!isset($asset['options']['output']) && isset($asset['output'])) {
            $asset['options']['output'] = $asset['output'];
        }

        $this->manager->setFormula($asset['name'], array(
            $asset['input'],
            $asset['filter'],
            $asset['options']
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * {@inheritDoc}
     */
    public function take()
    {
        $this->writeOutputLine("updating assets...", 2);

        // load formula loaders, done lazily to avoid circular dependencies
        foreach ($this->formula_loader_manager->getTypes() as $type) {
            $this->manager->setLoader($type, $this->formula_loader_manager->getLoader($type));
        }

        // load template resources
        foreach ($this->site->getViews() as $view) {
            $this->resource_loader->load($view, $this->manager);
        }

        // load configuration assets

        // write output assets
        $this->writer->writeManagerAssets($this->manager);

        $this->writeOutputLine("done", 2);
    }
}
