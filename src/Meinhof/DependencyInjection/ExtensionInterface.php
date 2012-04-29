<?php

namespace Meinhof\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface as BaseExtensionInterface;

interface ExtensionInterface extends BaseExtensionInterface
{
    public function getConfigurationResources($key);
}