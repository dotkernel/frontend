<?php

declare(strict_types=1);

namespace Frontend\Slug\Middleware;

use Frontend\Slug\SlugInterface;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Dot\AnnotatedServices\Annotation\Inject;

class SlugMiddleware implements MiddlewareInterface
{

    /** @var SlugInterface $slugAdapter */
    private SlugInterface $slugAdapter;

    /**
     * SlugMiddleware constructor.
     * @param SlugInterface $slugAdapter
     * @Inject({SlugInterface::class})
     */
    public function __construct(
        SlugInterface $slugAdapter
    ) {
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
