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

use function is_array;

class LanguageController extends AbstractActionController
{
    protected TranslateServiceInterface $translateService;
    protected RouterInterface $router;
    protected TemplateRendererInterface $template;
    protected array $translatorConfig = [];

    /**
     * @param array $translatorConfig
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
        TemplateRendererInterface $template,
        array $translatorConfig
    ) {
        $this->translateService = $translateService;
        $this->router           = $router;
        $this->template         = $template;
        $this->translatorConfig = $translatorConfig;
    }

    public function changeAction(): ResponseInterface
    {
        $data        = $this->getRequest()->getParsedBody();
        $languageKey = ! empty($data['languageKey']) ? $data['languageKey'] : $this->translatorConfig['default'];
        $this->translateService->addTranslatorCookie($languageKey);

        return new HtmlResponse('');
    }

    public function translateTextAction(): ResponseInterface
    {
        $translation = '';
        $request     = $this->getRequest();

        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $data = $request->getParsedBody();

            $text = ! empty($data['text']) ? $data['text'] : '';

            if (is_array($text)) {
                foreach ($text as $textItem) {
                    $translation .=
                        $this->template->render(
                            'language::translate-text.html.twig',
                            ['translateThis' => $textItem]
                        ) . '<br/>';
                }
            } else {
                $translation = $this->template->render(
                    'language::translate-text.html.twig',
                    ['translateThis' => $text]
                );
            }
        }

        return new JsonResponse([
            'translation' => $translation,
        ]);
    }
}
