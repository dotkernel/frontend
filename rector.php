<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    /**
     * @see https://getrector.com/documentation
     *
     * Run full analysis: vendor/bin/rector process --clear-cache
     * Run analysis as simulation: vendor/bin/rector process --dry-run --clear-cache
     * Run analysis only on specific directory: vendor/bin/rector process dir1 dir2 dirN --clear-cache
     * Get help using Rector: vendor/bin/rector --help
     */

    /**
     * Configure directories to be analyzed
     */
    $rectorConfig->paths([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/data',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/test',
    ]);

    /**
     * Exclude directories from being analyzed
     */
    $rectorConfig->skip([
        __DIR__ . '/data/cache',
    ]);

    /**
     * Select rules to be used by the analysis
     */
    $rectorConfig->sets([
        SetList::ACTION_INJECTION_TO_CONSTRUCTOR_INJECTION,
        SetList::PHP_81,
        SetList::PHP_82,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::NAMING,
        SetList::PRIVATIZATION,
        SetList::PSR_4,
        SetList::TYPE_DECLARATION,
    ]);
};
