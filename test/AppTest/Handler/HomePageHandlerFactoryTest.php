<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use App\Handler\HomePageHandlerFactory;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;

final class HomePageHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface|ObjectProphecy */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $router = $this->prophesize(RouterInterface::class);

        $this->container->get(RouterInterface::class)->willReturn($router);
    }

    public function testFactoryWithoutTemplate(): void
    {
        $homePageHandlerFactory = new HomePageHandlerFactory();
        $this->container->has(TemplateRendererInterface::class)->willReturn(false);
        $this->container->has(\Mezzio\Template\TemplateRendererInterface::class)->willReturn(false);

        $this->assertInstanceOf(HomePageHandlerFactory::class, $homePageHandlerFactory);

        $homePage = $homePageHandlerFactory($this->container->reveal());

        $this->assertInstanceOf(HomePageHandler::class, $homePage);
    }

    public function testFactoryWithTemplate(): void
    {
        $this->container->has(TemplateRendererInterface::class)->willReturn(true);
        $this->container
            ->get(TemplateRendererInterface::class)
            ->willReturn($this->prophesize(TemplateRendererInterface::class));

        $homePageHandlerFactory = new HomePageHandlerFactory();

        $homePage = $homePageHandlerFactory($this->container->reveal());

        $this->assertInstanceOf(HomePageHandler::class, $homePage);
    }
}
