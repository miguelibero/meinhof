<?php

namespace Meinhof\Action;

use Assetic\AssetWriter;
use Assetic\Factory\LazyAssetManager;

use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Assetic\ResourceLoaderInterface;
use Meinhof\Assetic\ResourceListerInterface;
use Meinhof\Assetic\FormulaLoaderManagerInterface;

/**
 * Compiles the site assets.
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class UpdateAssetsAction extends OutputAction
{
    protected $manager;
    protected $writer;
    protected $resourceLoader;
    protected $resourceLister;
    protected $formulaLoaderManager;
    protected $output;
    protected $assets = array();

    public function __construct(LazyAssetManager $manager,
        AssetWriter $writer, ResourceListerInterface $resourceLister,
        ResourceLoaderInterface $resourceLoader,
        FormulaLoaderManagerInterface $formulaLoaderManager,
        OutputInterface $output=null)
    {
        $this->manager = $manager;
        $this->writer = $writer;
        $this->resourceLoader = $resourceLoader;
        $this->resourceLister = $resourceLister;
        $this->formulaLoaderManager = $formulaLoaderManager;
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
        foreach ($this->formulaLoaderManager->getTypes() as $type) {
            $loader = $this->formulaLoaderManager->getLoader($type);
            $this->manager->setLoader($type, $loader);
        }

        // load template resources
        foreach ($this->resourceLister->getResources() as $resource) {
            $this->resourceLoader->load($resource, $this->manager);
        }

        // write output assets
        $this->writer->writeManagerAssets($this->manager);

        $this->writeOutputLine("done", 2);
    }
}
