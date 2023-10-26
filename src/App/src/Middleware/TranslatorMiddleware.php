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

use function array_key_exists;
use function bind_textdomain_codeset;
use function bindtextdomain;
use function putenv;
use function rtrim;
use function setlocale;
use function textdomain;

use const LC_ALL;

/**
 * @Service()
 */
class TranslatorMiddleware implements MiddlewareInterface
{
    /**
     * @Inject({
     *     TranslateServiceInterface::class,
     *     TemplateRendererInterface::class,
     *     "config.translator"
     * })
     */
    public function __construct(
        protected TranslateServiceInterface $translateService,
        protected TemplateRendererInterface $template,
        protected array $translatorConfig
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies   = $request->getCookieParams();
        $cookieKey = $this->translatorConfig['cookie']['name'] ?? '';

        // add language key
        if (isset($cookies[$cookieKey]) && array_key_exists($cookies[$cookieKey], $this->translatorConfig['locale'])) {
            $languageKey = $cookies[$cookieKey];
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
