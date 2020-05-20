<?php

namespace Frontend\Page\Controller;

use Dot\Controller\AbstractActionController;
use Frontend\Page\Service\PageService;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Dot\AnnotatedServices\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class PageController extends AbstractActionController
{

    /** @var RouterInterface $router */
    protected RouterInterface $router;

    /** @var PageService $pageService */
    protected PageService $pageService;

    /** @var TemplateRendererInterface $template */
    protected TemplateRendererInterface $template;

    /**
     * PageController constructor.
     * @param PageService $pageService
     * @param RouterInterface $router
     * @param TemplateRendererInterface $template
     *
     * @Inject({PageService::class, RouterInterface::class, TemplateRendererInterface::class})
     */
    public function __construct(PageService $pageService, RouterInterface $router, TemplateRendererInterface $template)
    {
        $this->pageService = $pageService;
        $this->router = $router;
        $this->template = $template;
    }

    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::home', [

            ])
        );
    }

    /**
     * @return ResponseInterface
     */
    public function homeAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::home', [

            ])
        );
    }

    /**
     * @return ResponseInterface
     */
    public function aboutUsAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::about', [

            ])
        );
    }

    /**
     * @return ResponseInterface
     */
    public function premiumContentAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::premium-content', [

            ])
        );
    }

    /**
     * @return ResponseInterface
     */
    public function whoWeAreAction(): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::who-we-are', [

            ])
        );
    }
}
