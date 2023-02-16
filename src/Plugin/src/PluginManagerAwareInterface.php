<?php

declare(strict_types=1);

namespace Frontend\Plugin;

/**
 * Interface PluginManagerAwareInterface
 * @package Frontend\Plugin
 */
interface PluginManagerAwareInterface
{
    /**
     * @param PluginManager $plugins
     * @return void
     */
    public function setPluginManager(PluginManager $plugins): void;

    /**
     * @return PluginManager
     */
    public function getPluginManager(): PluginManager;
}
