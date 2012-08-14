<?php

namespace Meinhof\Action;

use Symfony\Component\Console\Output\OutputInterface;

use Meinhof\Model\LoaderInterface;
use Meinhof\Export\ExporterInterface;

/**
 * This action calls the exporter on a list of models
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class UpdateModelsAction extends OutputAction
{
    protected $loader;
    protected $exporter;
    protected $globals;
    protected $output;

    public function __construct(
        LoaderInterface $loader,
        ExporterInterface $exporter,
        OutputInterface $output=null,
        array $globals=array()
    )
    {
        $this->loader = $loader;
        $this->exporter = $exporter;
        $this->globals = $globals;
        $this->output = $output;
    }

    protected function getOutput()
    {
        return $this->output;
    }

    public function take()
    {
        $models = $this->loader->getModels();
        $count = count($models);
        $plural = $this->loader->getModelsName();
        $singular = $this->loader->getModelName();

        $this->writeOutputLine(sprintf("updating %d %s...", $count, $plural), 2);

        foreach ($models as $model) {
            $params = $this->globals;
            $params[$singular] = $model;
            $template = $this->loader->getViewTemplatingKey($model);
            $this->exporter->export($model, $template, $params);
            $this->writeOutput(".", 1);
        }
        $this->writeOutputLine("", 1);
        $this->writeOutputLine("done", 2);
    }
}
