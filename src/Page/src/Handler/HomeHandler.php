<?php

declare(strict_types=1);

namespace Frontend\Page\Handler;

use Dot\AnnotatedServices\Annotation\Inject;
use Frontend\Page\Service\PageService;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class HomeHandler
 * @package Frontend\Page\Handler
 */
class HomeHandler implements RequestHandlerInterface
{
    /** @var RouterInterface $router */
    protected $router;

    /** @var PageService $pageService */
    protected $pageService;

    /** @var TemplateRendererInterface $template */
    protected $template;

    /**
     * BaseHandler constructor.
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
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::home', [

            ])
        );
    }
}
