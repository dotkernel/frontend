<?php

declare(strict_types=1);

namespace Frontend\Plugin;

/**
 * Interface PluginManagerAwareInterface
 * @package Frontend\Plugin
 */
interface PluginManagerAwareInterface
{
    public function setPluginManager(PluginManager $pluginManager): void;

    public function getPluginManager(): PluginManager;
}
