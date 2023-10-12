<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Service;

use Frontend\App\Service\CookieServiceInterface;
use Frontend\App\Service\TranslateService;
use Frontend\App\Service\TranslateServiceInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class TranslateServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillCreate(): void
    {
        $service = new TranslateService($this->createMock(CookieServiceInterface::class), []);

        $this->assertInstanceOf(TranslateServiceInterface::class, $service);
    }

    /**
     * @throws Exception
     */
    public function testAddTranslatorCookie(): void
    {
        $config = [
            'translator' => [
                'cookie' => [
                    'lifetime' => 100,
                    'name' => 'cookie_name',
                ],
            ],
        ];
        $cookieService = $this->createMock(CookieServiceInterface::class);
        $cookieService->expects($this->once())->method('setCookie');


        $service = new TranslateService($cookieService, $config);

        $service->addTranslatorCookie('en');
    }
}