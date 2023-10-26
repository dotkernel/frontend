<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\Entity\User;
use Frontend\User\Exception\AuthenticationAdapterException;
use Frontend\User\Factory\AuthenticationAdapterFactory;
use Laminas\Authentication\Adapter\AdapterInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function sprintf;

class AuthenticationAdapterFactoryTest extends TestCase
{
    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvokeThrowsRuntimeErrorEntityManagerNotFound(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->willReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf(
            "Class '%s' not found in container.",
            EntityManager::class
        ));

        (new AuthenticationAdapterFactory())($container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testInvokeThrowsErrorInvalidConfigurationProvided(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->willReturn(true);
        $container->expects($this->exactly(2))->method('get')->willReturn(null);

        $this->expectException(AuthenticationAdapterException::class);
        $this->expectExceptionMessage(
            (AuthenticationAdapterException::invalidConfigurationProvided())->getMessage()
        );

        (new AuthenticationAdapterFactory())($container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testInvokeThrowsErrorRepositoryNotFound(): void
    {
        $config = [
            'doctrine' => [
                'authentication' => [
                    'orm_default' => [
                        'identity_class' => User::class,
                    ],
                ],
            ],
        ];

        $container     = $this->createMock(ContainerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->once())->method('getRepository')->willReturn(null);
        $container->expects($this->once())->method('has')->willReturn(true);
        $container
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($entityManager, $config);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Could not find repository for identity class: %s',
                $config['doctrine']['authentication']['orm_default']['identity_class']
            )
        );

        (new AuthenticationAdapterFactory())($container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillInstantiate(): void
    {
        $config = [
            'doctrine' => [
                'authentication' => [
                    'orm_default' => [
                        'identity_class' => User::class,
                    ],
                ],
            ],
        ];

        $container     = $this->createMock(ContainerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository    = $this->createMock(EntityRepository::class);

        $entityManager->expects($this->once())->method('getRepository')->willReturn($repository);
        $container->expects($this->once())->method('has')->willReturn(true);
        $container
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($entityManager, $config);

        $adapter = (new AuthenticationAdapterFactory())($container);

        $this->assertInstanceOf(AuthenticationAdapter::class, $adapter);
        $this->assertInstanceOf(AdapterInterface::class, $adapter);
    }
}
