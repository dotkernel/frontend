<?php

declare(strict_types=1);

namespace Frontend\Plugin;

use Doctrine\DBAL\Driver\Exception;
use Frontend\Slug\Exception\MissingConfigurationException;
use Frontend\Slug\SlugInterface;
use Mezzio\Helper\UrlHelper;

/**
 * Class UrlHelperPlugin
 * @package Frontend\Plugin
 */
final class UrlHelperPlugin implements PluginInterface
{
    private readonly UrlHelper $urlHelper;
    private readonly SlugInterface $slug;

    /**
     * UrlHelperPlugin constructor.
     */
    public function __construct(UrlHelper $urlHelper, SlugInterface $slug)
    {
        $this->urlHelper = $urlHelper;
        $this->slug = $slug;
    }

    /**
     * @param string|null $routeName
     * @param $fragmentIdentifier
     * @return string|$this
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function __invoke(
        string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        $fragmentIdentifier = null,
        array $options = []
    ): UrlHelperPlugin|string {
        $args = func_get_args();
        if ($args === []) {
            return $this;
        }

        return $this->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }

    /**
     * @param string|null $routeName
     * @param null $fragmentIdentifier
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function generate(
        string $routeName = null,
        array $routeParams = [],
        array $queryParams = [],
        ?string $fragmentIdentifier = null,
        array $options = []
    ): string {

        $response = $this->slug->match($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);

        if ($response->isSuccess()) {
            return $response->getUrl();
        }

        return $this->urlHelper->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
    }
}
