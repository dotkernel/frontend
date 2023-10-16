<?php

declare(strict_types=1);

namespace Frontend\App\Service;

/**
 * Interface UserService
 */
interface CookieServiceInterface
{
    public function setCookie(string $name, mixed $value, ?array $options = []): bool;

    public function expireCookie(string $name): bool;
}
