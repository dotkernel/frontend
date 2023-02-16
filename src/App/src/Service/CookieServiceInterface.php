<?php

declare(strict_types=1);

namespace Frontend\App\Service;

/**
 * Interface UserService
 * @package Frontend\App\Service
 */
interface CookieServiceInterface
{
    /**
     * @param string $name
     * @param $value
     * @param array|null $options
     * @return bool
     */
    public function setCookie(string $name, $value, ?array $options = []): bool;

    /**
     * @param string $name
     * @return bool
     */
    public function expireCookie(string $name): bool;
}
