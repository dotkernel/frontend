<?php

declare(strict_types=1);

namespace Frontend\Slug;

use Doctrine\DBAL\Driver\Exception;
use FastRoute\RouteParser\Std;
use Frontend\Slug\Exception\DuplicateSlugException;
use Frontend\Slug\Exception\RuntimeException;
use Frontend\Slug\Exception\MissingConfigurationException;
use Frontend\Slug\Service\SlugServiceInterface;
use InvalidArgumentException;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Mezzio\Router\RouteResult;
use Mezzio\Router\RouterInterface;
use Mezzio\Helper\UrlHelper;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class SlugCollector
 * @package Frontend\Slug
 */
final class SlugCollector implements SlugInterface
{
    /**
     * @var string
     */
    private const REMOVABLE_PART = 'action';

    /**
     * Regular expression used to validate fragment identifiers.
     * @var string
     */
    private const FRAGMENT_IDENTIFIER_REGEX = '/^([!$&\'()*+,;=._~:@\/?-]|%[0-9a-fA-F]{2}|[a-zA-Z0-9])+$/';

    /**
     * List of all slugs registered directly with the application.
     *
     * @var Slug[] $slugs
     */
    private array $slugs = [];

    private readonly RouterInterface $router;
    private readonly ?DuplicateSlugDetector $duplicateSlugDetector;
    private readonly UrlHelper $urlHelper;
    private readonly SlugServiceInterface $slugService;
    private readonly bool $detectDuplicates;
    private array $config;

    /**
     * SlugCollector constructor.
     * @throws DuplicateSlugException
     */
    public function __construct(
        RouterInterface $router,
        UrlHelper $urlHelper,
        SlugServiceInterface $slugService,
        array $config = [],
        bool $detectDuplicates = true
    ) {
        $this->router = $router;
        $this->urlHelper = $urlHelper;
        $this->slugService = $slugService;
        $this->config = $config;
        $this->detectDuplicates = $detectDuplicates;

        $this->duplicateSlugDetector = new DuplicateSlugDetector();

        try {
            $this->loadConfig($config);
        } catch (DuplicateSlugException $duplicateSlugException) {
            throw new DuplicateSlugException($duplicateSlugException->getMessage(), $duplicateSlugException->getCode());
        }
    }

    /**
     * Load configuration parameters
     *
     * @param array $config Array of custom configuration options.
     * @throws DuplicateSlugException
     */
    public function loadConfig(array $config): void
    {
        if ($config === []) {
            return;
        }

        if (isset($this->config['slug_route'])) {
            foreach ($this->config['slug_route'] as $slugRoute) {
                $params = [];
                $params['action'] = $slugRoute['action'];
                $this->slug($slugRoute['alias'], $slugRoute['route'], $params, $slugRoute['exchange'] ?? []);
            }
        }
    }

    /**
     * Add a slug for the slug middleware to match.
     * @throws DuplicateSlugException
     */
    public function slug(
        string $alias,
        string $routeName,
        array $params,
        array $exchange
    ): Slug {
        $slug = new Slug($alias, $routeName, $params, $exchange);
        if ($this->detectDuplicates) {
            $this->detectDuplicate($slug);
        }

        $this->slugs[] = $slug;
        return $slug;
    }

    /**
     * Retrieve all directly registered slugs with the application.
     *
     * @return Slug[]
     */
    public function getSlugs(): array
    {
        return $this->slugs;
    }

    /**
     * @param $routeName
     * @param $routeParams
     * @param $queryParams
     * @param $fragmentIdentifier
     * @param $options
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function match(
        $routeName,
        $routeParams,
        $queryParams,
        $fragmentIdentifier,
        $options
    ): SlugResult {
        /** @var Slug $slug */
        $slug = array_reduce($this->slugs, static function ($matched, $slug) use ($routeName, $routeParams, $queryParams) {
            if ($routeName !== $slug->getRouteName()) {
                return $matched;
            }

            if ($routeParams['action'] !== $slug->getParams()['action']) {
                return $matched;
            }

            return $slug;
        }, false);

        $routerOptions = $options['router'] ?? [];
        $path = $this->router->generateUri($routeName, $routeParams, $routerOptions);

        $request = new ServerRequest();
        $request = $request->withUri(new Uri($path));

        $match = $this->router->match($request);
        $slug->setType(Slug::URL_TYPE);

        if ($match->isSuccess()) {
            $path = $this->generateUri($slug, $match);
            $path = $this->appendQueryStringArguments($path, $queryParams);
            $path = $this->appendFragment($path, $fragmentIdentifier);
        } else {
            $path = $this->urlHelper->generate($routeName, $routeParams, $queryParams, $fragmentIdentifier, $options);
        }

        return SlugResult::fromSlug($slug, $path);
    }

    /**
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function matchRequest(ServerRequestInterface $request): SlugResult
    {
        $path = rawurldecode($request->getUri()->getPath());
        $fragments = explode('/', $path);
        $path = '/' . $fragments[1];

        /** @var Slug $slug */
        $slug = array_reduce($this->slugs, static function ($matched, $slug) use ($path) {
            if ($path !== $slug->getAlias()) {
                return $matched;
            }
            
            return $slug;
        }, false);

        $path = $this->router->generateUri($slug->getRouteName(), $slug->getParams(), $fragments);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withUri(new Uri($path));

        $match = $this->router->match($serverRequest);

        $matchParams = $this->matchParams($request, $match);

        $slug->setType(Slug::REQUEST_TYPE);

        if ($match->isSuccess()) {
            $path = $this->generateUri($slug, $match, $matchParams);
        } else {
            return SlugResult::fromSlugFailure();
        }

        return SlugResult::fromSlug($slug, $path, $matchParams);
    }

    /**
     * @throws MissingConfigurationException
     */
    public function generateUri(Slug $slug, RouteResult $routeResult, array $matchParams = []): string
    {
        $route = $routeResult->getMatchedRoute();
        $substitutions = $routeResult->getMatchedParams();

        if ($slug->getType() === Slug::REQUEST_TYPE) {
            $substitutions = array_merge($routeResult->getMatchedParams(), $matchParams);
        }

        $std = new Std();
        $routes = array_reverse($std->parse($route->getPath()));
        $missingParameters = [];
        foreach ($routes as $route) {
            // Check if all parameters can be substituted
            $missingParameters = $this->missingParameters($route, $substitutions);
            // If not all parameters can be substituted, try the next route
            if ($missingParameters !== []) {
                continue;
            }

            $path = $slug->getAlias();
            if ($slug->getType() === Slug::REQUEST_TYPE) {
                $path = '';
            }

            foreach ($route as $p => $part) {
                if ($slug->getType() === Slug::URL_TYPE) {
                    if (is_string($part)) {
                        // Append the string
                        $path .= '';
                        continue;
                    }

                    if ($part[0] !== self::REMOVABLE_PART) {
                        // Check substitute value with regex
                        /** @psalm-suppress UndefinedVariable */
                        if (!empty($addOns)) {
                            $substitutions[$part[0]] = $addOns[$p];
                        }

                        if (!preg_match('~^' . $part[1] . '$~', (string)$substitutions[$part[0]])) {
                            throw new RuntimeException(
                                sprintf(
                                    'Parameter value for [%s] did not match the regex `%s`',
                                    $part[0],
                                    $part[1]
                                )
                            );
                        }

                        $attribute = $substitutions[$part[0]];
                        if (!empty($slug->getExchange())) {
                            $attribute = $this->slugService->slugManipulation(
                                $slug,
                                $part[0],
                                $attribute
                            );
                        }

                        // Append the substituted value
                        $path .= '/' . $attribute;
                    }
                } else {
                    if (is_string($part)) {
                        // Append the string
                        if ($part !== '/') {
                            $path .= $part;
                        }

                        continue;
                    }

                    if (!preg_match('~^' . $part[1] . '$~', (string)$substitutions[$part[0]])) {
                        throw new RuntimeException(
                            sprintf(
                                'Parameter value for [%s] did not match the regex `%s`',
                                $part[0],
                                $part[1]
                            )
                        );
                    }

                    $attribute = $substitutions[$part[0]];
                    if ($part[0] !== self::REMOVABLE_PART) {
                        if (!empty($slug->getExchange())) {
                            $attribute = $this->slugService->slugManipulation(
                                $slug,
                                $part[0],
                                $attribute
                            );
                        }

                        if ($attribute) {
                            $path .= '/' . $attribute;
                        }
                    } else {
                        $path .= $attribute;
                    }
                }
            }

            // Return generated path
            return $path;
        }

        // No valid route was found: list minimal required parameters
        throw new RuntimeException(sprintf(
            'Route `%s` expects at least parameter values for [%s], but received [%s]',
            $routeResult->getMatchedRouteName(),
            implode(',', $missingParameters),
            implode(',', array_keys($substitutions))
        ));
    }

    /**
     * Checks for any missing route parameters
     */
    private function missingParameters(array $parts, array $substitutions): array
    {
        $missingParameters = [];

        foreach ($parts as $part) {
            if (is_string($part)) {
                continue;
            }

            $missingParameters[] = $part[0];
        }

        foreach ($missingParameters as $missingParameter) {
            if (! isset($substitutions[$missingParameter])) {
                return $missingParameters;
            }
        }

        return [];
    }

    private function appendQueryStringArguments(string $uriString, array $queryParams): string
    {
        if ($queryParams !== []) {
            return sprintf('%s?%s', $uriString, http_build_query($queryParams));
        }

        return $uriString;
    }

    private function appendFragment(string $uriString, ?string $fragmentIdentifier): string
    {
        if ($fragmentIdentifier !== null) {
            if (! preg_match(self::FRAGMENT_IDENTIFIER_REGEX, $fragmentIdentifier)) {
                throw new InvalidArgumentException('Fragment identifier must conform to RFC 3986', 400);
            }

            return sprintf('%s#%s', $uriString, $fragmentIdentifier);
        }

        return $uriString;
    }

    public function matchParams(ServerRequestInterface $serverRequest, RouteResult $routeResult): array
    {
        $route = $routeResult->getMatchedRoute();
        $std = new Std();
        $routes = array_reverse($std->parse($route->getPath()));

        $fragments = explode('/', $serverRequest->getUri()->getPath());

        $mParams = [];
        foreach ($routes as $route) {
            // Generate the path
            $validParams = [];

            foreach ($route as $part) {
                if ($part[0] !== '/' && $part[0] !== 'action') {
                    $validParams[] = $part[0];
                }
            }

            foreach ($validParams as $p => $part) {
                if (isset($fragments[$p + 2])) {
                    $mParams[$part] = $fragments[$p + 2];
                }
            }

            return $mParams;
        }

        return [];
    }

    /**
     * @throws DuplicateSlugException
     */
    private function detectDuplicate(Slug $slug): void
    {
        $this->duplicateSlugDetector?->detectDuplicate($slug);
    }
}
