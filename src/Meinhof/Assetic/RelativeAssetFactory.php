<?php

namespace Meinhof\Assetic;

use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetInterface;

/**
 * Adds an options to specify a base path for the target path
 * of the created assets
 */
class RelativeAssetFactory extends AssetFactory
{
    protected $base_target_path;

    public function setBaseTargetPath($base)
    {
        $this->base_target_path = rtrim($base,'/');
    }

    public function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
        $asset = parent::createAsset($inputs, $filters, $options);
        if($asset instanceof AssetInterface){
            $path = $asset->getTargetPath();
            if($this->base_target_path && substr($path,0,1) !== '/'){
                $path = $this->base_target_path.'/'.$path;
            }
            $asset->setTargetPath($path);
        }
        return $asset;
    }
}