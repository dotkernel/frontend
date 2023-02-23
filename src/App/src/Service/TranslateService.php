<?php

declare(strict_types=1);

namespace Frontend\App\Service;

use Dot\AnnotatedServices\Annotation\Inject;

/**
 * Class TranslateService
 * @package Frontend\App\Service
 */
final class TranslateService implements TranslateServiceInterface
{
    private readonly CookieServiceInterface $cookieService;
    private array $config = [];

    /**
     * TranslateService constructor.
     *
     * @Inject({
     *     CookieServiceInterface::class,
     *     "config"
     * })
     */
    public function __construct(
        CookieServiceInterface $cookieService,
        array $config = []
    ) {
        $this->cookieService = $cookieService;
        $this->config = $config;
    }

    public function addTranslatorCookie(string $languageKey): void
    {
        $expires = time() +
            ($this->config['translator']['cookie']['lifetime'] ?? $this->config['session_config']['cookie_lifetime']);

        $this->cookieService->setCookie($this->config['translator']['cookie']['name'], $languageKey, [
            'expires' => $expires,
        ]);
    }
}
