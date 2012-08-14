<?php

namespace Meinhof\Helper;

/**
 * Helper class that delegates to a list of helpers.
 */
class DelegatingUrlHelper implements UrlHelperInterface
{
    protected $helpers = array();

    public function __construct(array $helpers=array())
    {
        foreach ($this->helpers as $class=>$helper) {
            if (!$helper instanceof UrlHelperInterface) {
                throw new \InvalidArgumentException("Invalid helper.");
            }
            $this->addHelper($class, $helper);
        }
    }

    public function addHelper($class, UrlHelperInterface $helper)
    {
        $this->helpers[$class] = $helper;
    }

    public function getUrl($model, array $parameters)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException("Model needs to be an object.");
        }
        foreach ($this->helpers as $class=>$helper) {
            if ($model instanceof $class) {
                return $helper->getUrl($model, $parameters);
            }
        }
        throw new \RuntimeException("Could not find helper for class ".get_class($model));
    }
}
