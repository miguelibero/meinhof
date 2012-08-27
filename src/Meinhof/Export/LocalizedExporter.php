<?php

namespace Meinhof\Export;

use Symfony\Component\Translation\TranslatorInterface;
/**
 * Exports each element for a list of locales
 *
 * @author Miguel Ibero <miguel@ibero.me>
 */
class LocalizedExporter implements ExporterInterface
{
    protected $locales;
    protected $exporter;
    protected $translator;

    public function __construct(array $locales, ExporterInterface $exporter, TranslatorInterface $translator)
    {
        $this->locales = $locales;
        $this->exporter = $exporter;
        $this->translator = $translator;
    }

    public function export($model, $template, array $parameters)
    {
        foreach ($this->locales as $locale) {
            $parameters['locale'] = $locale;
            $parameters['locales'] = $this->locales;
            $this->translator->setLocale($locale);
            $this->exporter->export($model, $template, $parameters);
        }
    }
}
