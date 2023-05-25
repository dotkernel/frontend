<?php

declare(strict_types=1);

namespace Frontend\User\Factory;

use Doctrine\ORM\EntityManager;
use Exception;
use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\Repository\UserRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AuthenticationAdapter
 * @package Frontend\User\Factory
 */
class AuthenticationAdapterFactory
{
    /**
     * @param ContainerInterface $container
     * @return AuthenticationAdapter
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): AuthenticationAdapter
    {
        if (!$container->has(EntityManager::class)) {
            throw new Exception('EntityManager not found.');
        }

        $config = $container->get('config');
        if (!isset($config['doctrine']['authentication']['orm_default'])) {
            throw new Exception('Authentication config not found.');
        }

        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        $repository = $entityManager->getRepository(
            $config['doctrine']['authentication']['orm_default']['identity_class']
        );
        if (!$repository instanceof UserRepository) {
            throw new Exception(
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
