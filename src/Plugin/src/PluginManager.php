<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * @extends AbstractPluginManager<mixed>
 */
class PluginManager extends AbstractPluginManager
{
    /** @var string $instanceOf */
    protected $instanceOf = PluginInterface::class;
}
