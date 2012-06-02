<?php

namespace Meinhof\Assetic;

use Assetic\Factory\AssetFactory as BaseAssetFactory;

/**
 * Overwrites the base assetic factory to fix some problems.
 *
 * First problem is repeated roots generate different asset names.
 *     $options['root'] = array('/path', '/path')
 *     $options['root'] = '/path'
 * These two options should generate the same asset name.
 *
 * Second problem is creating an asset with only one input and output
 * pat empty or '*' should generate the same output path.
 * '/images/loading.gif' -> '*' should be changed to
 * '/images/loading.gif' -> '/images/loading.gif'
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class AssetFactory extends BaseAssetFactory
{
    /**
     * {@inheritDoc}
     */
    public function generateAssetName($inputs, $filters, $options = array())
    {
        // Fixes a problem with bad roots
        if (is_array($options) && isset($options['root'])) {
            if (!is_array($options['root'])) {
                $options['root'] = array($options['root']);
            }
            // remove empty elements
            $options['root'] = array_filter($options['root']);
            // remove duplicate roots
            $options['root'] = array_unique($options['root']);
        }

        return parent::generateAssetName($inputs, $filters, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
        // Fixes problem with generated output
        if (!is_array($inputs)) {
            $inputs = array($inputs);
        }
        // set output the same as output if only one input
        if (count($inputs) === 1 && (!isset($options['output'])  || $options['output'] === "*")) {
            $options['output'] = reset($inputs);
        }

        return parent::createAsset($inputs, $filters, $options);
    }
}
