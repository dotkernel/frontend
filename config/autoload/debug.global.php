<?php

/**
 * This is a helper file that can be required by other config files to determine the debug status.
 */

declare(strict_types=1);

$path = getcwd() . '/config/development.config.php';
if (! file_exists($path)) {
    return [
        '_debug' => false,
    ];
}

$config = require $path;
return [
    '_debug' => $config['debug'] ?? false,
];
