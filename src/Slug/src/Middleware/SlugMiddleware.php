<?php

declare(strict_types=1);

namespace Frontend\Slug\Middleware;

use Frontend\Slug\SlugInterface;
use Frontend\Slug\Service\SlugService;
use Laminas\Diactoros\Uri;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Dot\AnnotatedServices\Annotation\Inject;

class SlugMiddleware implements MiddlewareInterface
{

    /** @var RouterInterface */
    protected RouterInterface $router;

    /** @var SlugInterface */
    private SlugInterface $slugAdapter;

    /** @var SlugService */
    protected SlugService $slugService;

    /** @var array $config */
    private array $config;

    /**
     * SlugMiddleware constructor.
     * @param RouterInterface $router
     * @param SlugService $slugService
     * @param array $config
     * @param SlugInterface $slugAdapter
     * @Inject({RouterInterface::class, SlugService::class, "config.slug_configuration",
     *     SlugInterface::class})
     */
    public function __construct(
        RouterInterface $router,
        SlugService $slugService,
        array $config,
        SlugInterface $slugAdapter
    ) {
        $this->router       = $router;
        $this->slugService  = $slugService;
        $this->config       = $config;
        $this->slugAdapter  = $slugAdapter;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->slugAdapter->matchRequest($request);

        if ($result->isSuccess()) {
            $request = $request->withUri(new Uri($result->getUrl()));
        }

        return $handler->handle($request);
    }
}
