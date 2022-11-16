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
class TranslatorMiddleware implements MiddlewareInterface
{
    /** @var  TranslateServiceInterface */
    protected $translateService;

    /** @var TemplateRendererInterface */
    protected $template;

    /** @var array */
    protected $translatorConfig;

    /**
     * TranslatorMiddleware constructor.
     * @param TranslateServiceInterface $translateService
     * @param TemplateRendererInterface $template
     * @param array $translatorConfig
     *
     * @Inject({TranslateServiceInterface::class, TemplateRendererInterface::class, "config.translator"})
     */
    public function __construct(
        TranslateServiceInterface $translateService,
        TemplateRendererInterface $template,
        array $translatorConfig
    ) {
        $this->translateService = $translateService;
        $this->template = $template;
        $this->translatorConfig = $translatorConfig;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookieKey = $this->translatorConfig['cookie']['name'];

        // add language key
        if (isset($_COOKIE[$cookieKey]) && array_key_exists($_COOKIE[$cookieKey], $this->translatorConfig['locale'])) {
            $languageKey = $_COOKIE[$cookieKey];
        } else {
            $languageKey = $this->translatorConfig['default'];

            // set language
            $this->translateService->addTranslatorCookie($languageKey);
        }

        $this->template->addDefaultParam(
            TemplateRendererInterface::TEMPLATE_ALL,
            'language_key',
            rtrim($languageKey, '/')
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

        return $handler->handle($request);
    }
}
