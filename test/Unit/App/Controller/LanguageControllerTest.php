<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Controller;

use Frontend\App\Controller\LanguageController;
use Frontend\App\Service\TranslateService;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class LanguageControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWillCreate(): void
    {
        $controller = new LanguageController(
            $this->createMock(TranslateService::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(TemplateRendererInterface::class),
            []
        );

        $this->assertInstanceOf(LanguageController::class, $controller);
    }
}