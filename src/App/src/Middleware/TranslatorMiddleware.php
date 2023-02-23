<?php

declare(strict_types=1);

namespace Frontend\App\Middleware;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\AnnotatedServices\Annotation\Service;
use Frontend\App\Service\TranslateServiceInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class TranslatorMiddleware
 * @package Frontend\App\Middleware
 *
 * @Service()
 */
final class TranslatorMiddleware implements MiddlewareInterface
{
    private readonly TranslateServiceInterface $translateService;
    private readonly TemplateRendererInterface $templateRenderer;
    private array $translatorConfig = [];

    /**
     * TranslatorMiddleware constructor.
     *
     * @Inject({
     *     TranslateServiceInterface::class,
     *     TemplateRendererInterface::class,
     *     "config.translator"
     * })
     */
    public function __construct(
        TranslateServiceInterface $translateService,
        TemplateRendererInterface $templateRenderer,
        array $translatorConfig
    ) {
        $this->translateService = $translateService;
        $this->templateRenderer = $templateRenderer;
        $this->translatorConfig = $translatorConfig;
    }

    public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $cookies = $serverRequest->getCookieParams();
        $cookieKey = $this->translatorConfig['cookie']['name'];

        // add language key
        if (isset($cookies[$cookieKey]) && array_key_exists($cookies[$cookieKey], $this->translatorConfig['locale'])) {
            $languageKey = $cookies[$cookieKey];
        } else {
            $languageKey = $this->translatorConfig['default'];

            // set language
            $this->translateService->addTranslatorCookie($languageKey);
        }

        $this->templateRenderer->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'language_key',
            rtrim((string) $languageKey, '/')
        );

        $language = $this->translatorConfig['locale'][$languageKey];
        putenv('LC_ALL=' . $language);
        putenv('LANG=' . $language);
        putenv('LANGUAGE=' . $language);
        setlocale(LC_ALL, $language);

        $domain = $this->translatorConfig['domain'];
        //Specify the location of the translation tables
        $baseDir = $this->translatorConfig['base_dir'];
        bindtextdomain($domain, $baseDir);
        bind_textdomain_codeset($domain, $this->translatorConfig['code_set']);

        //Choose domain
        textdomain($domain);

        return $requestHandler->handle($serverRequest);
    }
}
