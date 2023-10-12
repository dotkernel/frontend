<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Contact;

use Frontend\Contact\RoutesDelegator;
use Mezzio\Application;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RoutesDelegatorTest extends TestCase
{
    /**
     * @throws Exception
     */
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