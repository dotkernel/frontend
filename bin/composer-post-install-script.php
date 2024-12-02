<?php

declare(strict_types=1);

function copyFile(string $source, string $destination): void
{
    if (! copy($source, $destination)) {
        echo "Could not copy $source to $destination." . PHP_EOL;
        exit(1);
    }
}

function validateInput(array $options, string $projectRoot): array
{
    $sourceFile           = $options['sourceFile'] ?? null;
    $destinationDirectory = $options['destinationDirectory'] ?? null;

    $sourceFilePath = realpath($projectRoot . DIRECTORY_SEPARATOR . $sourceFile);
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
$options     = getopt("", ["sourceFile:", "destinationDirectory:"]);

[$sourceFilePath, $destinationDirectoryPath] = validateInput($options, $projectRoot);

$sourceFileName             = basename($sourceFilePath);
$destinationFilePath        = $destinationDirectoryPath . DIRECTORY_SEPARATOR . $sourceFileName;
$destinationFileWithoutDist = str_replace('.dist', '', $destinationFilePath);

if (! file_exists($destinationFileWithoutDist)) {
    copyFile($sourceFilePath, $destinationFileWithoutDist);
    echo "Files copied successfully from {$options['sourceFile']} to {$options['destinationDirectory']}." . PHP_EOL;
}

exit(0);
