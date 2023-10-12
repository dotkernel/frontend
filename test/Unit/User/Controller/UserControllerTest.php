<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User\Controller;

use Dot\DebugBar\DebugBar;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\CookieServiceInterface;
use Frontend\Plugin\FormsPlugin;
use Frontend\User\Controller\UserController;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Authentication\AuthenticationService;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillCreate(): void
    {
        $controller = new UserController(
            $this->createMock(CookieServiceInterface::class),
            $this->createMock(UserServiceInterface::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(TemplateRendererInterface::class),
            $this->createMock(AuthenticationService::class),
            $this->createMock(FlashMessengerInterface::class),
            $this->createMock(FormsPlugin::class),
            $this->createMock(DebugBar::class),
            [],
        );

        $this->assertInstanceOf(UserController::class, $controller);
    }
}