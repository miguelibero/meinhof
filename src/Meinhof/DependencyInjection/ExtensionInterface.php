<?php

namespace MeinHof\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface as BaseExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ExtensionInterface extends BaseExtensionInterface
{
    /**
     * This function is called before the compiling.
     * It is usefull to add custom compiler passes and configuration.
     */
    public function preload(ContainerBuilder $container);
}
