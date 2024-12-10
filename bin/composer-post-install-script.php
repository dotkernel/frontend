<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

function copyFile(array $file): void
{
    if (is_readable($file['destination'])) {
        echo "File {$file['destination']} already exists." . PHP_EOL;
    } else {
        if (! isDevModeEnabled() && $file['environment'] === 'local') {
            echo "Skipping the copy of {$file['source']} due to environment settings." . PHP_EOL;
        } else {
            if (! copy($file['source'], $file['destination'])) {
                echo "Cannot copy {$file['source']} file to {$file['destination']}" . PHP_EOL;
            } else {
                echo "File {$file['source']} copied successfully to {$file['destination']}." . PHP_EOL;
            }
        }
    }
}

function isDevModeEnabled(): bool
{
    return file_exists('config/autoload/development.local.php');
}

// when adding files to the below array the `source` and `destination` must be relative to the project root folder
// the `environment` key will indicate when the file should be copied,
// if the value is `production` the file will be copied on both production and local environments,
// if the value is `local` the file will be copied only on local environments
$files = [
    [
        'source'      => 'config/autoload/local.php.dist',
        'destination' => 'config/autoload/local.php',
        'environment' => 'production',
    ],
    [
        'source'      => 'config/autoload/local.test.php.dist',
        'destination' => 'config/autoload/local.test.php',
        'environment' => 'local',
    ],
    [
        'source'      => 'vendor/dotkernel/dot-mail/config/mail.global.php.dist',
        'destination' => 'config/autoload/mail.global.php',
        'environment' => 'production',
    ],
];

array_walk($files, 'copyFile');
