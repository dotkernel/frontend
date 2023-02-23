<?php

declare(strict_types=1);

namespace Frontend\Slug\Middleware;

use Doctrine\DBAL\Driver\Exception;
use Dot\AnnotatedServices\Annotation\Inject;
use Frontend\Slug\Exception\MissingConfigurationException;
use Frontend\Slug\SlugInterface;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class SlugMiddleware
 * @package Frontend\Slug\Middleware
 */
final class SlugMiddleware implements MiddlewareInterface
{
    private readonly SlugInterface $slug;

    /**
     * SlugMiddleware constructor.
     * @Inject({
     *     SlugInterface::class
     * })
     */
    public function __construct(SlugInterface $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function process(ServerRequestInterface $serverRequest, RequestHandlerInterface $requestHandler): ResponseInterface
    {
        $result = $this->slug->matchRequest($serverRequest);

        if ($result->isSuccess()) {
            $serverRequest = $serverRequest->withUri(new Uri($result->getUrl()));
        }

        return $requestHandler->handle($serverRequest);
    }
}
