<?php

namespace Meinhof\Model;

/**
 * Loads a list of models
 */
interface LoaderInterface
{
    /**
     * Should return the singular name of the model.
     * @return string singular model name
     */
    public function getModelName();

    /**
     * Should return the plural name of the model.
     * @return string plural model name
     */
    public function getModelsName();

    /**
     * Returns an array of model objects
     * @return array model objects
     */
    public function getModels();

    /**
     * Should return a model that has a given key
     * @return mixed the model object
     */
    public function getModel($key);

    /**
     * Should return a template name to render the model
     * @return string template name
     */
    public function getViewTemplatingKey($model);
}
