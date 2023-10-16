<?php

declare(strict_types=1);

namespace Frontend\App\Service;

/**
 * Interface TranslateServiceInterface
 */
interface TranslateServiceInterface
{
    public function addTranslatorCookie(string $languageKey): void;
}
