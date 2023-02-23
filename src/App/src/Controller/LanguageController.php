<?php

declare(strict_types=1);

namespace Frontend\App\Controller;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\Controller\AbstractActionController;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\App\Service\TranslateServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class LanguageController
 * @package Frontend\App\Controller
 */
final class LanguageController extends AbstractActionController
{
    private readonly TranslateServiceInterface $translateService;
    private readonly TemplateRendererInterface $templateRenderer;
    private array $translatorConfig = [];

    /**
     * LanguageController constructor.
     *
     * @Inject({
     *     TranslateServiceInterface::class,
     *     RouterInterface::class,
     *     TemplateRendererInterface::class,
     *     "config.translator"
     * })
     */
    public function __construct(
        TranslateServiceInterface $translateService,
        RouterInterface $router,
        TemplateRendererInterface $templateRenderer,
        array $translatorConfig
    ) {
        $this->translateService = $translateService;
        $this->templateRenderer = $templateRenderer;
        $this->translatorConfig = $translatorConfig;
    }

    public function changeAction(): ResponseInterface
    {
        $parsedBody = $this->getRequest()->getParsedBody();
        $languageKey = (empty($parsedBody['languageKey'])) ? $this->translatorConfig['default'] : $parsedBody['languageKey'];
        $this->translateService->addTranslatorCookie($languageKey);

        return new HtmlResponse('');
    }

    public function translateTextAction(): ResponseInterface
    {
        $translation = '';
        $request = $this->getRequest();

        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $parsedBody = $request->getParsedBody();

            $text = (empty($parsedBody['text'])) ? '' : $parsedBody['text'];

            if (is_array($text)) {
                foreach ($text as $textItem) {
                    $translation = $translation .
                        $this->templateRenderer->render(
                            'language::translate-text.html.twig',
                            ['translateThis' => $textItem]
                        ) . '<br/>';
                }
            } else {
                $translation = $this->templateRenderer->render(
                    'language::translate-text.html.twig',
                    ['translateThis' => $text]
                );
            }
        }

        return new JsonResponse([
            'translation' => $translation
        ]);
    }
}
