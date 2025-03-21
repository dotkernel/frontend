<?php

namespace Frontend\App\Handler;

use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Dot\DependencyInjection\Attribute\Inject;

class GetIndexRedirectHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
    )]
    public function __construct(protected RouterInterface $router) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new RedirectResponse($this->router->generateUri('page::home'));
    }
}