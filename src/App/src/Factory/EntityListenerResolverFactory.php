<?php

declare(strict_types=1);

namespace Frontend\App\Factory;

use Frontend\App\Resolver\EntityListenerResolver;
use Psr\Container\ContainerInterface;

class EntityListenerResolverFactory
{
    public function __invoke(ContainerInterface $container): EntityListenerResolver
    {
        return new EntityListenerResolver($container);
    }
}
