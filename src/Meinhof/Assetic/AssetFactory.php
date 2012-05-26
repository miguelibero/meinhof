<?php

namespace Meinhof\Assetic;

use Assetic\Factory\AssetFactory as BaseAssetFactory;

class AssetFactory extends BaseAssetFactory
{
    /**
     * Fixes a problem with bad roots
     */
    public function generateAssetName($inputs, $filters, $options = array())
    {
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
     * Fixes problem with generated output
     */
    public function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
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
