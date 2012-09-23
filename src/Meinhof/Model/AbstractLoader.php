<?php

namespace Meinhof\Model;

abstract class AbstractLoader implements LoaderInterface
{
    public function getModelsName()
    {
        return $this->getModelName().'s';
    }

    public function getViewTemplatingKey($model)
    {
        $closure = array($model, 'getViewTemplatingKey');
        if (is_callable($closure)) {
            return $closure();
        } else {
            return $this->getModelName();
        }
    }

    public function getModel($key)
    {
        $models = $this->getModels();
        foreach ($models as $model) {
            if ($model instanceof CategoryInterface) {
                if ($model->getKey() == $key) {
                    return $model;
                }
            }
        }
        throw new \RuntimeException("Category with key '${key}' not found.");
    }
}
