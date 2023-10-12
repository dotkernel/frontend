<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Page;

use Frontend\Page\RoutesDelegator;
use Mezzio\Application;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RoutesDelegatorTest extends TestCase
{
    public function testWillInvoke(): void
    {
        $application = (new RoutesDelegator())(
            $this->createMock(ContainerInterface::class),
            '',
            function () {
                return $this->createMock(Application::class);
            }
        );

        $this->assertInstanceOf(Application::class, $application);
    }
}