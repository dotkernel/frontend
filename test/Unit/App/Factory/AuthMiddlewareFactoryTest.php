<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Factory;

use Dot\FlashMessenger\FlashMessenger;
use Dot\Rbac\Guard\Options\RbacGuardOptions;
use Dot\Rbac\Guard\Provider\GuardsProviderInterface;
use Dot\Rbac\Guard\Provider\GuardsProviderPluginManager;
use Frontend\App\Factory\AuthMiddlewareFactory;
use Frontend\App\Middleware\AuthMiddleware;
use Laminas\ConfigAggregator\ArrayProvider;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use PHPUnit\Framework\MockObject\Exception;

class AuthMiddlewareFactoryTest extends TestCase
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillInvoke(): void
    {
        $guardsProviderPluginManager = $this->createMock(GuardsProviderPluginManager::class);
        $guardsProviderPluginManager
            ->expects($this->once())
            ->method('get')
            ->willReturn(new class implements GuardsProviderInterface {
                public function getGuards(): array
                {
                    return [];
                }
            });

        $routerInterface = $this->createMock(RouterInterface::class);
        $flashMessenger  = $this->createMock(FlashMessenger::class);
        $container       = $this->createMock(ContainerInterface::class);

        $rbacGuardOptions = new RbacGuardOptions([]);
        $rbacGuardOptions->setGuardsProvider([
            'type' => ArrayProvider::class,
        ]);

        $container->method('get')->willReturnMap([
            [GuardsProviderPluginManager::class, $guardsProviderPluginManager],
            [RbacGuardOptions::class, $rbacGuardOptions],
            [RouterInterface::class, $routerInterface],
            [FlashMessenger::class, $flashMessenger],
        ]);

        $service = (new AuthMiddlewareFactory())($container, AuthMiddleware::class);
        $this->assertInstanceOf(AuthMiddleware::class, $service);
    }
}