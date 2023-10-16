<?php

declare(strict_types=1);

namespace Frontend\Plugin;

/**
 * Interface PluginManagerAwareInterface
 */
interface PluginManagerAwareInterface
{
    public function setPluginManager(PluginManager $plugins): void;

    public function getPluginManager(): PluginManager;
}
