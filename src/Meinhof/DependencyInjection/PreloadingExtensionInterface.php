<?php

namespace MeinHof\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

interface PreloadingExtensionInterface extends ExtensionInterface
{
    public function preload();
}
