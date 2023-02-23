<?php

declare(strict_types=1);

namespace Frontend\User\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Exception;
use Frontend\User\Adapter\AuthenticationAdapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AuthenticationAdapter
 * @package Frontend\User\Factory
 */
final class AuthenticationAdapterFactory
{
    /**
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
        $entityRepository = $entityManager->getRepository(
            $config['doctrine']['authentication']['orm_default']['identity_class']
        );
        if (!($entityRepository instanceof EntityRepository)) {
            throw new Exception(
                sprintf(
                    'Could not find repository for identity class: %s',
                    $config['doctrine']['authentication']['orm_default']['identity_class']
                )
            );
        }

        return new AuthenticationAdapter(
            $entityRepository,
            $config['doctrine']['authentication']['orm_default']
        );
    }
}
