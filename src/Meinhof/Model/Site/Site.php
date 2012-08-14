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
        if (substr($name, 0, 3) === 'get') {
            // getCamelCase -> camel_case
            $name = Container::underscore(substr($name, 3));
        }
        if (!isset($this->modelLoaders[$name])) {
            throw new \InvalidArgumentException("Could not find models of type ${name}.");
        }

        return $this->modelLoaders[$name]->getModels();
    }

    public function __call($method, array $params)
    {
        if (count($params) === 0) {
            return $this->getModels($method);
        } else {
            throw new \InvalidParameterException("Method `${method}` not found.");
        }
    }
}
