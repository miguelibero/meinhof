<?php

namespace Meinhof\Exporter;

use Meinhof\Helper\UrlHelperInterface;
use Symfony\Component\Templating\EngineInterface;

class FilesystemSiteExporter extends AbstractSiteExporter
{
    protected $base_path;
    protected $base_url;

    public function __construct(
        EngineInterface $engine,
        UrlHelperInterface $url,
        $base_path)
    {
        parent::__construct($engine, $url);
        $this->base_path = $base_path;
    }

    protected function saveUrl($url, $content)
    {
        $path = $this->base_path.'/'.trim($url,'/');
        @mkdir(dirname($path), 0755, true);
        if (@file_put_contents($path, $content) === false) {
            throw new \RuntimeException("Could not save url '${url}'.");
        }
    }
}
