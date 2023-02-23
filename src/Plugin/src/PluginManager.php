<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package Frontend\Plugin
 */
final class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = PluginInterface::class;
}
