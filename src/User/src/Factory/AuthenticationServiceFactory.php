<?php

declare(strict_types=1);

namespace Frontend\User\Factory;

use Frontend\User\Adapter\AuthenticationAdapter;
use Laminas\Authentication\AuthenticationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AuthenticationAdapter
 * @package Frontend\User\Factory
 */
final class AuthenticationServiceFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AuthenticationService
    {
        return new AuthenticationService(
            null,
            $container->get(AuthenticationAdapter::class)
        );
    }
}
