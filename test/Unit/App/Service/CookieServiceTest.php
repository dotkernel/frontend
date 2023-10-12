<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Service;

use Frontend\App\Service\CookieService;
use Frontend\App\Service\CookieServiceInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\Exception;
use Laminas\Session\Config\ConfigInterface;
use Laminas\Session\SessionManager;

class CookieServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillCreate(): void
    {
        $sessionManager = $this->createMock(SessionManager::class);
        $config = $this->createMock(ConfigInterface::class);
        $sessionManager->expects($this->once())->method('getConfig')->willReturn($config);

        $service = new CookieService($sessionManager);

        $this->assertInstanceOf(CookieServiceInterface::class, $service);
    }

    /**
     * @throws Exception
     */
    public function testSetCookieUseCookiesDisabled(): void
    {
        $sessionManager = $this->createMock(SessionManager::class);
        $config = $this->createMock(ConfigInterface::class);
        $sessionManager->expects($this->once())->method('getConfig')->willReturn($config);

        $service = new CookieService($sessionManager);

        $this->assertFalse($service->setCookie('cookie_name', 'cookie_value'));
    }

    /**
     * @throws Exception
     */
    public function testSetCookieUseCookiesEnabled(): void
    {
        $sessionManager = $this->createMock(SessionManager::class);
        $config = $this->createMock(ConfigInterface::class);

        $config->expects($this->once())->method('getUseCookies')->willReturn(true);
        $sessionManager->expects($this->once())->method('getConfig')->willReturn($config);

        $service = new CookieService($sessionManager);

        $this->assertTrue($service->setCookie(
            'cookie_name',
            'cookie_value',
            [
                'expires' => 3600 * 24 * 30,
                'domain' => 'domain',
                'httponly' => false,
                'path' => '/',
                'samesite' => 'Lax',
                'secure' => false,
            ]
        ));
    }
}