<?php

namespace Meinhof\Export;

/**
 * Exports each element for a list of locales
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class LocalizedExporter implements ExporterInterface
{
    protected $locales;
    protected $exporter;

    public function __construct(array $locales, ExporterInterface $exporter)
    {
        $this->locales = $locales;
        $this->exporter = $exporter;
    }

    public function export($model, $template, array $parameters)
    {
        foreach ($this->locales as $locale) {
            $parameters['locale'] = $locale;
            $this->exporter->export($model, $template, $parameters);
        }
    }  
}
