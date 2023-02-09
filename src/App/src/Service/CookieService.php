<?php

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
     * @param SessionManager $sessionManager
     *
     * @Inject({
     *     SessionManager::class
     * })
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionConfig = $sessionManager->getConfig();
    }

    /**
     * @param string $name
     * @param $value
     * @param array|null $options
     * @return bool
     */
    public function setCookie(string $name, $value, ?array $options = []): bool
    {
        if (!$this->sessionConfig->getUseCookies()) {
            return false;
        }

        return setcookie($name, $value, $this->getMergedOptions($options));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function expireCookie(string $name): bool
    {
        return setcookie($name, '', $this->getMergedOptions([
            'expires' => time() - 86400
        ]));
    }

    /**
     * @param array|null $options
     * @return array
     */
    private function getMergedOptions(?array $options = []): array
    {
        return [
            'expires'  => $options['expires']  ?? $this->getCookieLifetime(),
            'domain'   => $options['domain']   ?? $this->sessionConfig->getCookieDomain(),
            'httponly' => $options['httponly'] ?? $this->sessionConfig->getCookieHttpOnly(),
            'path'     => $options['path']     ?? $this->sessionConfig->getCookiePath(),
            'samesite' => $options['samesite'] ?? $this->sessionConfig->getCookieSameSite(),
            'secure'   => $options['secure']   ?? $this->sessionConfig->getCookieSecure(),
        ];
    }

    /**
     * @return int
     */
    private function getCookieLifetime(): int
    {
        return time() + $this->sessionConfig->getCookieLifetime();
    }
}
