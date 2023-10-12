<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Middleware;

use Frontend\App\Middleware\TranslatorMiddleware;
use Frontend\App\Service\TranslateServiceInterface;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TranslatorMiddlewareTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testWillCreate(): void
    {
        $middleware = new TranslatorMiddleware(
            $this->createMock(TranslateServiceInterface::class),
            $this->createMock(TemplateRendererInterface::class),
            []
        );

        $this->assertInstanceOf(TranslatorMiddleware::class, $middleware);
    }

    /**
     * @throws Exception
     */
    public function testProcessSwitchLanguage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $service = $this->createMock(TranslateServiceInterface::class);
        $template = $this->createMock(TemplateRendererInterface::class);

        $request->expects($this->once())->method('getCookieParams')->willReturn([
            'dk30Translator' => 'ro'
        ]);

        $languageKey = 'ro';
        $language = 'ro_RO';
        $template->expects($this->once())->method('addDefaultParam')->with(
            TemplateRendererInterface::TEMPLATE_ALL,
            'language_key',
            rtrim($languageKey, '/')
        );

        $middleware = new TranslatorMiddleware(
            $service,
            $template,
            $this->getTranslatorConfig(),
        );

        $middleware->process($request, $handler);

        $this->assertSame($language, getenv('LC_ALL=') . $language);
        $this->assertSame($language, getenv('LANG=') . $language);
        $this->assertSame($language, getenv('LANGUAGE=') . $language);
    }

    /**
     * @throws Exception
     */
    public function testProcessDefaultLanguage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $service = $this->createMock(TranslateServiceInterface::class);
        $template = $this->createMock(TemplateRendererInterface::class);

        $languageKey = 'en';
        $language = 'en_EN';
        $template->expects($this->once())->method('addDefaultParam')->with(
            TemplateRendererInterface::TEMPLATE_ALL,
            'language_key',
            rtrim($languageKey, '/')
        );

        $service->expects($this->once())->method('addTranslatorCookie')->with($languageKey);

        $middleware = new TranslatorMiddleware(
            $service,
            $template,
            $this->getTranslatorConfig(),
        );

        $middleware->process($request, $handler);

        $this->assertSame($language, getenv('LC_ALL=') . $language);
        $this->assertSame($language, getenv('LANG=') . $language);
        $this->assertSame($language, getenv('LANGUAGE=') . $language);
    }

    private function getTranslatorConfig(): array
    {
        return [
            'cookie' => [
                'name' => 'dk30Translator',
                'lifetime' => 3600 * 24 * 30,
            ],
            'default' => 'en',
            'locale' => [
                'en' => 'en_EN',
                'ro' => 'ro_RO',
            ],
            'code_set' => 'UTF-8',
            'domain' => 'messages',
            'base_dir' => getcwd() . '/data/language',
        ];
    }
}