<?php

namespace Meinhof\Model\Site;

use Symfony\Component\DependencyInjection\Container;

use Meinhof\Model\LoaderInterface;

class Site implements SiteInterface
{
    protected $modelLoaders = array();

    public function __construct(array $info)
    {
        $this->info = $info;
    }

    public function addModelLoader(LoaderInterface $loader)
    {
        $this->modelLoaders[$loader->getModelsName()] = $loader;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getModels($name)
    {
        if(!isset($this->modelLoaders[$name])){
            throw new \InvalidArgumentException("Could not find models of type ${name}.");
        }
        return $this->modelLoaders[$name]->getModels();
    }

    public function __call($method, array $params)
    {
        return $this->getModels($method);
    }
}
