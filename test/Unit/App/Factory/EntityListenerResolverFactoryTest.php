<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Factory;

use Frontend\App\Factory\EntityListenerResolverFactory;
use Frontend\App\Resolver\EntityListenerResolver;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class EntityListenerResolverFactoryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillInvoke(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $service = (new EntityListenerResolverFactory())($container);
        $this->assertInstanceOf(EntityListenerResolver::class, $service);
    }
}