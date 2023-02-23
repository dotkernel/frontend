<?php

declare(strict_types=1);

namespace Frontend\Slug\TwigExtension;

use Doctrine\DBAL\Driver\Exception;
use Frontend\Slug\Exception\MissingConfigurationException;
use Frontend\Slug\SlugInterface;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RouteExtension
 * @package Frontend\Slug\TwigExtension
 */
final class RouteExtension extends AbstractExtension
{
    private readonly UrlHelper $urlHelper;
    private readonly SlugInterface $slug;
    private readonly ServerUrlHelper $serverUrlHelper;

    /**
     * RouteExtension constructor.
     */
    public function __construct(
        UrlHelper $urlHelper,
        SlugInterface $slug,
        ServerUrlHelper $serverUrlHelper
    ) {
        $this->urlHelper = $urlHelper;
        $this->slug = $slug;
        $this->serverUrlHelper = $serverUrlHelper;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', $this->renderUri(...)),
            new TwigFunction('url', $this->renderUrl(...)),
        ];
    }

    /**
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function renderUri(
        ?string $route = null,
        array $routeParams = [],
        array $queryParams = [],
        ?string $fragmentIdentifier = null,
        array $options = []
    ): string {
        $response = $this->slug->match($route, $routeParams, $queryParams, $fragmentIdentifier, $options);

        if ($response->isSuccess()) {
            return $response->getUrl();
        }

        return $this->urlHelper->generate($route, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }

    /**
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function renderUrl(
        ?string $route = null,
        array $routeParams = [],
        array $queryParams = [],
        ?string $fragmentIdentifier = null,
        array $options = []
    ): string {
        return $this->serverUrlHelper->generate(
            $this->renderUri($route, $routeParams, $queryParams, $fragmentIdentifier, $options)
        );
    }
}
