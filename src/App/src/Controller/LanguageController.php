<?php

declare(strict_types=1);

namespace Frontend\App\Controller;

use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Service\TranslateServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

use function is_array;

class LanguageController extends AbstractActionController
{
    #[Inject(
        TranslateServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        "config.translator",
    )]
    public function __construct(
        protected TranslateServiceInterface $translateService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected array $translatorConfig
    ) {
    }

    public function changeAction(): ResponseInterface
    {
        $data        = $this->getRequest()->getParsedBody();
        $languageKey = ! empty($data['languageKey']) ? $data['languageKey'] : $this->translatorConfig['default'];
        $this->translateService->addTranslatorCookie($languageKey);

        return new EmptyResponse(StatusCodeInterface::STATUS_OK);
    }

    public function translateTextAction(): ResponseInterface
    {
        $translation = '';
        $data        = $this->getRequest()->getParsedBody();
        $text        = ! empty($data['text']) ? $data['text'] : '';
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

        return new JsonResponse([
            'translation' => $translation,
        ]);
    }
}
