<?php

declare(strict_types=1);

namespace Frontend\Page\Controller;

use Dot\Controller\AbstractActionController;
use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Page\Service\PageServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;

use function rtrim;

class PageController extends AbstractActionController
{
    #[Inject(
        PageServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        "config",
    )]
    public function __construct(
        protected PageServiceInterface $pageService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected array $config = []
    ) {
    }

    public function indexAction(): ResponseInterface
    {
        $canonicalUrl = rtrim($this->config['application']['url'], '/') . $this->router->generateUri('home');
        return new HtmlResponse(
            $this->template->render('page::home', ['canonicalUrl' => $canonicalUrl])
        );
    }

    public function homeAction(): ResponseInterface
    {
        $canonicalUrl = rtrim($this->config['application']['url'], '/') . $this->router->generateUri('home');
        return new HtmlResponse(
            $this->template->render('page::home', ['canonicalUrl' => $canonicalUrl])
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
