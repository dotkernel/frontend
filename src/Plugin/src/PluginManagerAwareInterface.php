<?php

declare(strict_types=1);

namespace Frontend\Plugin;

interface PluginManagerAwareInterface
{
    public function setPluginManager(PluginManager $plugins): void;

    public function getPluginManager(): PluginManager;
}
