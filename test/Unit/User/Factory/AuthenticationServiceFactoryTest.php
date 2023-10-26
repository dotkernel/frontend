<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Factory;

use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\Factory\AuthenticationServiceFactory;
use Laminas\Authentication\AuthenticationServiceInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AuthenticationServiceFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillInstantiate(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $adapter   = $this->createMock(AuthenticationAdapter::class);

        $container->expects($this->once())->method('get')->willReturn($adapter);

        $service = (new AuthenticationServiceFactory())($container);

        $this->assertInstanceOf(AuthenticationServiceInterface::class, $service);
    }
}
