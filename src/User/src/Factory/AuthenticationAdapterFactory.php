<?php

declare(strict_types=1);

namespace Frontend\User\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\Exception\AuthenticationAdapterException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class AuthenticationAdapterFactory
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): AuthenticationAdapter
    {
        if (! $container->has(EntityManager::class)) {
            throw new RuntimeException(
                sprintf(
                    "Class '%s' not found in container.",
                    EntityManager::class
                )
            );
        }

        $entityManager = $container->get(EntityManager::class);
        $config = $container->get('config');
        if (! isset($config['doctrine']['authentication']['orm_default'])) {
            throw AuthenticationAdapterException::invalidConfigurationProvided();
        }

        $repository = $entityManager->getRepository(
            $config['doctrine']['authentication']['orm_default']['identity_class']
        );
        if (! $repository instanceof EntityRepository) {
            throw new RuntimeException(
                sprintf(
                    'Could not find repository for identity class: %s',
                    $config['doctrine']['authentication']['orm_default']['identity_class']
                )
            );
        }

        return new AuthenticationAdapter(
            $repository,
            $config['doctrine']['authentication']['orm_default']
        );
    }
}
