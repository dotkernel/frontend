<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Twig\Extension;

use Frontend\App\Twig\Extension\RouteExtension;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Mezzio\Helper\UrlHelper;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

use function method_exists;

class RouteExtensionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillInstantiate(): void
    {
        $urlHelper      = $this->createMock(UrlHelper::class);
        $routeExtension = new RouteExtension($urlHelper);
        $this->assertInstanceOf(RouteExtension::class, $routeExtension);
    }

    /**
     * @throws Exception
     */
    public function testWillAddExistingFunctions(): void
    {
        $routeExtension = new RouteExtension(
            $this->createMock(UrlHelper::class)
        );

        $functions = $routeExtension->getFunctions();
        $this->assertCount(2, $functions);

        $twigFunction = $functions[0];
        $this->assertInstanceOf(TwigFunction::class, $twigFunction);

        $callable = $twigFunction->getCallable();
        $this->assertIsArray($callable);
        $this->assertCount(2, $callable);
        $this->assertInstanceOf(RouteExtension::class, $callable[0]);
        $this->assertTrue(method_exists($routeExtension, $callable[1]));
        $this->assertSame($twigFunction->getName(), $callable[1]);
    }

    /**
     * @throws Exception
     */
    public function testWillGetCurrentRoute(): void
    {
        $router    = $this->createMock(RouterInterface::class);
        $request   = new ServerRequest(uri: new Uri('/test'));
        $urlHelper = new UrlHelper($router);
        $urlHelper->setRequest($request);
        $routeExtension = new RouteExtension($urlHelper);
        $this->assertSame('/test', $routeExtension->getCurrentRoute());
    }

    /**
     * @throws Exception
     */
    public function testIsRoute(): void
    {
        $router    = $this->createMock(RouterInterface::class);
        $request   = new ServerRequest(uri: new Uri('/test'));
        $urlHelper = new UrlHelper($router);
        $urlHelper->setRequest($request);
        $routeExtension = new RouteExtension($urlHelper);
        $this->assertSame(true, $routeExtension->isRoute('/test'));
    }
}
