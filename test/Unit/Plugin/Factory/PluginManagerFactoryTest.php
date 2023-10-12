<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Plugin\Factory;

use Frontend\Plugin\Factory\PluginManagerFactory;
use Frontend\Plugin\PluginManager;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class PluginManagerFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillInstantiate(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturn([
            'dot_controller' => [
                'plugin_manager' => ['plugin manager']
            ],
        ]);

        $object = (new PluginManagerFactory())($container);

        $this->assertInstanceOf(PluginManager::class, $object);
    }
}