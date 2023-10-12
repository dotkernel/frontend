<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Resolver;

use Frontend\App\Resolver\EntityListenerResolver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;

class EntityListenerResolverTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillCreate(): void
    {
        $resolver = new EntityListenerResolver(
            $this->createMock(ContainerInterface::class)
        );
        $this->assertInstanceOf(EntityListenerResolver::class, $resolver);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillResolveNonExistingItem(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with('test')
            ->willReturn(new stdClass());

        $resolver = new EntityListenerResolver($container);
        $this->assertIsObject($resolver->resolve('test'));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillResolveExistingItem(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with(EntityListenerResolver::class)
            ->willReturn($this->createMock(EntityListenerResolver::class));

        $resolver = new EntityListenerResolver($container);
        $this->assertInstanceOf(
            EntityListenerResolver::class,
            $resolver->resolve(EntityListenerResolver::class)
        );
    }
}