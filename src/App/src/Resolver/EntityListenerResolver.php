<?php

declare(strict_types=1);

namespace Frontend\App\Resolver;

use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class EntityListenerResolver
 * @package Frontend\App\Resolver
 */
final class EntityListenerResolver extends DefaultEntityListenerResolver
{
    private readonly ContainerInterface $container;

    /**
     * EntityListenerResolver constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $className
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function resolve($className): object
    {
        return $this->container->get($className);
    }
}
