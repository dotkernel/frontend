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

class PageController extends AbstractActionController
{
    protected RouterInterface $router;
    protected PageServiceInterface $pageService;
    protected TemplateRendererInterface $template;

    /**
     * @Inject({
     *     PageServiceInterface::class,
     *     RouterInterface::class,
     *     TemplateRendererInterface::class
     * })
     */
    public function __construct(
        PageServiceInterface $pageService,
        RouterInterface $router,
        TemplateRendererInterface $template
    ) {
        $this->pageService = $pageService;
        $this->router      = $router;
        $this->template    = $template;
    }

    public function indexAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::home')
        );
    }

    public function homeAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::home')
        );
    }

    public function aboutUsAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::about')
        );
    }

    public function premiumContentAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::premium-content')
        );
    }

    public function whoWeAreAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::who-we-are')
        );
    }
}
