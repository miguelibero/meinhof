<?php

namespace Meinhof\Assetic;

use Meinhof\Exporter\ExportEvent;

/**
 * Fixes the asset factory to reflect the relative
 * target path
 */
class RelativeExportListener
{
    protected $factory;

    public function __construct(RelativeAssetFactory $factory)
    {
        $this->factory = $factory;
    }

    public function export(ExportEvent $event)
    {
        $url = $event->getUrl();
        $times = count(explode('/', dirname($url)));
        $base = str_repeat('../', $times);
        $this->factory->setBaseTargetPath($base);
    }
}