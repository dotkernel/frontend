<?php

declare(strict_types=1);

namespace Frontend\Slug;

use Doctrine\DBAL\Driver\Exception;
use Frontend\Slug\Exception\MissingConfigurationException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Interface SlugInterface
 * @package Frontend\Slug
 */
interface SlugInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return SlugResult
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function matchRequest(Request $request): SlugResult;

    /**
     * @param $routeName
     * @param $routeParams
     * @param $queryParams
     * @param $fragmentIdentifier
     * @param $options
     * @return SlugResult
     * @throws Exception
     * @throws MissingConfigurationException
     */
    public function match(
        $routeName,
        $routeParams,
        $queryParams,
        $fragmentIdentifier,
        $options
    ): SlugResult;

    /**
     * @return Slug[]
     */
    public function getSlugs(): array;
}
