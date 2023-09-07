<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

// To enable or disable caching, set the `ConfigAggregator::ENABLE_CACHE` boolean in
// `config/autoload/local.php`.
$cacheConfig = [
    'config_cache_path' => 'data/cache/config-cache.php',
];

$aggregator = new ConfigAggregator([
    \Laminas\HttpHandlerRunner\ConfigProvider::class,
    \Laminas\Diactoros\ConfigProvider::class,
    \Mezzio\Twig\ConfigProvider::class,
    \Mezzio\Router\FastRouteRouter\ConfigProvider::class,
    // Include cache configuration
    new ArrayProvider($cacheConfig),

    \Mezzio\Helper\ConfigProvider::class,
    \Mezzio\ConfigProvider::class,
    \Mezzio\Router\ConfigProvider::class,
    \Mezzio\Cors\ConfigProvider::class,

    // Swoole config to overwrite some services (if installed)
    class_exists(\Mezzio\Swoole\ConfigProvider::class)
        ? \Mezzio\Swoole\ConfigProvider::class
        : function(){ return[]; },

    // DotKernel packages
    \Dot\Session\ConfigProvider::class,
    \Dot\DebugBar\ConfigProvider::class,
    \Dot\Mail\ConfigProvider::class,
    \Laminas\Form\ConfigProvider::class,
    \Dot\Log\ConfigProvider::class,
    \Dot\ErrorHandler\ConfigProvider::class,
    \Dot\AnnotatedServices\ConfigProvider::class,
    \Dot\Twig\ConfigProvider::class,
    \Dot\FlashMessenger\ConfigProvider::class,
    \Dot\Rbac\ConfigProvider::class,
    \Dot\Rbac\Guard\ConfigProvider::class,
    \Dot\ResponseHeader\ConfigProvider::class,
    \Dot\DataFixtures\ConfigProvider::class,

    // Default App module config
    \Frontend\App\ConfigProvider::class,
    \Frontend\Contact\ConfigProvider::class,
    \Frontend\Page\ConfigProvider::class,
    \Frontend\Plugin\ConfigProvider::class,
    \Frontend\User\ConfigProvider::class,

    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider(realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'),

    // Load development config if it exists
    new PhpFileProvider(realpath(__DIR__) . '/development.config.php'),
], $cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();
