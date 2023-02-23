<?php

declare(strict_types=1);

namespace Frontend\Page\Controller;

use Dot\AnnotatedServices\Annotation\Inject;
use Dot\Controller\AbstractActionController;
use Frontend\Page\Service\PageServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PageController
 * @package Frontend\Page\Controller
 */
final class PageController extends AbstractActionController
{
    private readonly TemplateRendererInterface $templateRenderer;

    /**
     * PageController constructor.
     *
     * @Inject({
     *     PageServiceInterface::class,
     *     RouterInterface::class,
     *     TemplateRendererInterface::class
     * })
     */
    public function __construct(
        PageServiceInterface $pageService,
        RouterInterface $router,
        TemplateRendererInterface $templateRenderer
    ) {
        $this->templateRenderer = $templateRenderer;
    }

    public function indexAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->templateRenderer->render('page::home')
        );
    }

    public function homeAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->templateRenderer->render('page::home')
        );
    }

    public function aboutUsAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->templateRenderer->render('page::about')
        );
    }

    public function premiumContentAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->templateRenderer->render('page::premium-content')
        );
    }

    public function whoWeAreAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->templateRenderer->render('page::who-we-are')
        );
    }
}
