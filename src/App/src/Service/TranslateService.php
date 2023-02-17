<?php

declare(strict_types=1);

namespace Frontend\App\Service;

use Dot\AnnotatedServices\Annotation\Inject;

/**
 * Class TranslateService
 * @package Frontend\App\Service
 */
class TranslateService implements TranslateServiceInterface
{
    protected CookieServiceInterface $cookieService;
    protected array $config = [];

    /**
     * TranslateService constructor.
     * @param CookieServiceInterface $cookieService
     * @param array $config
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

    /**
     * @param string $languageKey
     * @return void
     */
    public function addTranslatorCookie(string $languageKey): void
    {
        $expires = time() +
            ($this->config['translator']['cookie']['lifetime'] ?? $this->config['session_config']['cookie_lifetime']);

        $this->cookieService->setCookie($this->config['translator']['cookie']['name'], $languageKey, [
            'expires' => $expires,
        ]);
    }
}
