<?php

declare(strict_types=1);

namespace Frontend\App\Service;

use Dot\AnnotatedServices\Annotation\Inject;

use function time;

class TranslateService implements TranslateServiceInterface
{
    protected CookieServiceInterface $cookieService;
    protected array $config = [];

    /**
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
        $this->config        = $config;
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
