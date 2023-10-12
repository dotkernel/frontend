<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Frontend\App\ConfigProvider;
use Frontend\App\Controller\LanguageController;
use Frontend\App\Resolver\EntityListenerResolver;
use Frontend\App\RoutesDelegator;
use Frontend\App\Service\CookieService;
use Frontend\App\Service\CookieServiceInterface;
use Frontend\App\Service\RecaptchaService;
use Frontend\App\Service\TranslateService;
use Frontend\App\Service\TranslateServiceInterface;
use Mezzio\Application;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config = [];

    protected function setup(): void
    {
        parent::setUp();

        $this->config = (new ConfigProvider())();
    }

    public function testConfigHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testConfigHasDoctrine(): void
    {
        $this->assertArrayHasKey('doctrine', $this->config);
    }

    public function testConfigHasTemplates(): void
    {
        $this->assertArrayHasKey('templates', $this->config);
    }

    public function testDependenciesHasDelegators(): void
    {
        $this->assertArrayHasKey('delegators', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['delegators']);
        $this->assertArrayHasKey(Application::class, $this->config['dependencies']['delegators']);
        $this->assertIsArray($this->config['dependencies']['delegators'][Application::class]);
        $this->assertContainsEquals(
            RoutesDelegator::class,
            $this->config['dependencies']['delegators'][Application::class]
        );
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['factories']);
        $this->assertArrayHasKey('doctrine.entity_manager.orm_default', $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(EntityListenerResolver::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(TranslateService::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(LanguageController::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(RecaptchaService::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(CookieService::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['aliases']);
        $this->assertArrayHasKey(EntityManager::class, $this->config['dependencies']['aliases']);
        $this->assertArrayHasKey(EntityManagerInterface::class, $this->config['dependencies']['aliases']);
        $this->assertArrayHasKey(TranslateServiceInterface::class, $this->config['dependencies']['aliases']);
        $this->assertArrayHasKey(CookieServiceInterface::class, $this->config['dependencies']['aliases']);
    }

    public function testGetDoctrineConfig(): void
    {
        $this->assertArrayHasKey('configuration', $this->config['doctrine']);
        $this->assertIsArray($this->config['doctrine']['configuration']);
        $this->assertArrayHasKey('orm_default', $this->config['doctrine']['configuration']);
        $this->assertIsArray($this->config['doctrine']['configuration']['orm_default']);
        $this->assertArrayHasKey(
            'entity_listener_resolver',
            $this->config['doctrine']['configuration']['orm_default']
        );
        $this->assertSame(
            EntityListenerResolver::class,
            $this->config['doctrine']['configuration']['orm_default']['entity_listener_resolver']
        );
    }

    public function testGetTemplates(): void
    {
        $this->assertArrayHasKey('paths', $this->config['templates']);
        $this->assertIsArray($this->config['templates']['paths']);
        $this->assertArrayHasKey('app', $this->config['templates']['paths']);
        $this->assertArrayHasKey('error', $this->config['templates']['paths']);
        $this->assertArrayHasKey('layout', $this->config['templates']['paths']);
        $this->assertArrayHasKey('partial', $this->config['templates']['paths']);
        $this->assertArrayHasKey('language', $this->config['templates']['paths']);
    }
}