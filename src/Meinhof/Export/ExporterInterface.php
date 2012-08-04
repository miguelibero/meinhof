<?php

namespace Meinhof\Export;

/**
 * Exports a model
 */
interface ExporterInterface
{
    public function export($model, $template, array $parameters);
}
