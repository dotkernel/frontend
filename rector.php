<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/data',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/test',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/data/cache',
    ]);

    // define sets of rules
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
