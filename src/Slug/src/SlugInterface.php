<?php

declare(strict_types=1);

namespace Frontend\Slug;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Interface SlugInterface
 * @package Frontend\Slug
 */
interface SlugInterface
{
    /**
     *
     * @param Request $request
     * @return SlugResult
     */
    public function matchRequest(Request $request): SlugResult;

    /**
     * @param $routeName
     * @param $routeParams
     * @param $queryParams
     * @param $fragmentIdentifier
     * @param $options
     * @return SlugResult
     */
    public function match(
        $routeName,
        $routeParams,
        $queryParams,
        $fragmentIdentifier,
        $options
    ): SlugResult;

    /**
     * @return array
     */
    public function getSlugs(): array;
}
