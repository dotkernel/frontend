<?php

declare(strict_types=1);

$debug = require getcwd() . '/config/autoload/debug.global.php';

return [
    'router' => [
        'fastroute' => [
            Mezzio\Router\FastRouteRouter::CONFIG_CACHE_ENABLED => ! $debug['_debug'],
        ],
    ],
];
