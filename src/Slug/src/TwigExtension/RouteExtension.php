<?php

declare(strict_types=1);

namespace Frontend\Slug\TwigExtension;

use Frontend\Slug\SlugInterface;
use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Helper\UrlHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RouteExtension
 * @package Frontend\App\Twig
 */
class RouteExtension extends AbstractExtension
{
    /** @var UrlHelper $urlHelper */
    private UrlHelper $urlHelper;

    /** @var SlugInterface $slugAdapter */
    private SlugInterface $slugAdapter;

    /** @var ServerUrlHelper $serverUrlHelper*/
    private ServerUrlHelper $serverUrlHelper;

    /**
     * RouteExtension constructor.
     * @param UrlHelper $urlHelper
     * @param SlugInterface $slugAdapter
     * @param ServerUrlHelper $serverUrlHelper
     */
    public function __construct(
        UrlHelper $urlHelper,
        SlugInterface $slugAdapter,
        ServerUrlHelper $serverUrlHelper
    ) {
        $this->urlHelper = $urlHelper;
        $this->slugAdapter = $slugAdapter;
        $this->serverUrlHelper = $serverUrlHelper;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'renderUri']),
            new TwigFunction('url', [$this, 'renderUrl']),
        ];
    }

    /**
     * @param string|null $route
     * @param array $routeParams
     * @param array $queryParams
     * @param string|null $fragmentIdentifier
     * @param array $options
     * @return string
     */
    public function renderUri(
        ?string $route = null,
        array $routeParams = [],
        array $queryParams = [],
        ?string $fragmentIdentifier = null,
        array $options = []
    ): string {

        $response = $this->slugAdapter->match($route, $routeParams, $queryParams, $fragmentIdentifier, $options);

        if ($response->isSuccess()) {
            return $response->getUrl();
        }

        return $this->urlHelper->generate($route, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }

    /**
     * @param string|null $route
     * @param array $routeParams
     * @param array $queryParams
     * @param string|null $fragmentIdentifier
     * @param array $options
     * @return string
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
