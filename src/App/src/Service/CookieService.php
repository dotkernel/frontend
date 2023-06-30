<?php

declare(strict_types=1);

namespace Frontend\App\Service;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Laminas\Session\Config\ConfigInterface;
use Laminas\Session\SessionManager;

/**
 * Class UserService
 * @package Frontend\App\Service
 *
 * @Service()
 */
class CookieService implements CookieServiceInterface
{
    private ConfigInterface $sessionConfig;

    /**
     * @Inject({
     *     SessionManager::class
     * })
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionConfig = $sessionManager->getConfig();
    }

    public function setCookie(string $name, mixed $value, ?array $options = []): bool
    {
        if (!$this->sessionConfig->getUseCookies()) {
            return false;
        }

        return setcookie($name, $value, $this->getMergedOptions($options));
    }

    public function expireCookie(string $name): bool
    {
        return setcookie($name, '', $this->getMergedOptions([
            'expires' => time() - 86400
        ]));
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     */
    private function getMergedOptions(?array $options = []): array
    {
        return [
            'expires' => $options['expires'] ?? $this->getCookieLifetime(),
            'domain' => $options['domain'] ?? $this->sessionConfig->getCookieDomain(),
            'httponly' => $options['httponly'] ?? $this->sessionConfig->getCookieHttpOnly(),
            'path' => $options['path'] ?? $this->sessionConfig->getCookiePath(),
            'samesite' => $options['samesite'] ?? $this->sessionConfig->getCookieSameSite(),
            'secure' => $options['secure'] ?? $this->sessionConfig->getCookieSecure(),
        ];
    }

    private function getCookieLifetime(): int
    {
        return time() + $this->sessionConfig->getCookieLifetime();
    }
}
