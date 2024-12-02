<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Composer\InstalledVersions;

const VENDOR_DIR = 'vendor';

function copyFile(string $source, string $destination): void
{
    if (! copy($source, $destination)) {
        echo "Could not copy $source to $destination." . PHP_EOL;
        exit(1);
    }
}

function validateInput(array $options, string $projectRoot): array
{
    $package              = $options['package'] ?? null;
    $sourceFile           = $options['sourceFile'] ?? null;
    $destinationDirectory = $options['destinationDirectory'] ?? null;

    if (empty($package) || ! InstalledVersions::isInstalled($package)) {
        echo "Error: Package $package is not installed." . PHP_EOL;
        exit(1);
    }

    $vendorDir      = $projectRoot . DIRECTORY_SEPARATOR . VENDOR_DIR;
    $sourceFilePath = realpath($vendorDir . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR . $sourceFile);

    if (! $sourceFilePath || ! file_exists($sourceFilePath)) {
        echo "Error: Source file $sourceFile does not exist." . PHP_EOL;
        exit(1);
    }

    $destDirectoryPath = realpath($projectRoot . DIRECTORY_SEPARATOR . $destinationDirectory);
    if (! $destDirectoryPath || ! is_dir($destDirectoryPath) || ! is_writable($destDirectoryPath)) {
        echo "Error: Destination directory $destinationDirectory does not exist or is not writable." . PHP_EOL;
        exit(1);
    }

    return [$sourceFilePath, $destDirectoryPath];
}

$projectRoot = realpath(__DIR__ . '/..');
$options     = getopt("", ["package:", "sourceFile:", "destinationDirectory:"]);

[$sourceFilePath, $destinationDirectoryPath] = validateInput($options, $projectRoot);

$sourceFileName             = basename($sourceFilePath);
$destinationFilePath        = $destinationDirectoryPath . DIRECTORY_SEPARATOR . $sourceFileName;
$destinationFileWithoutDist = str_replace('.dist', '', $destinationFilePath);

copyFile($sourceFilePath, $destinationFilePath);
copyFile($sourceFilePath, $destinationFileWithoutDist);

echo "Files copied successfully from {$options['package']}." . PHP_EOL;
exit(0);
