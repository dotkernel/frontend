<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Contact;

use Frontend\Contact\ConfigProvider;
use Frontend\Contact\Controller\ContactController;
use Frontend\Contact\Repository\MessageRepository;
use Frontend\Contact\RoutesDelegator;
use Frontend\Contact\Service\MessageService;
use Frontend\Contact\Service\MessageServiceInterface;
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

    public function testConfigHasForms(): void
    {
        $this->assertArrayHasKey('forms', $this->config);
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
        $this->assertArrayHasKey(ContactController::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(MessageService::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(MessageRepository::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['aliases']);
        $this->assertArrayHasKey(MessageServiceInterface::class, $this->config['dependencies']['aliases']);
    }

    public function testGetDoctrineConfig(): void
    {
        $this->assertArrayHasKey('driver', $this->config['doctrine']);
        $this->assertIsArray($this->config['doctrine']['driver']);
        $this->assertArrayHasKey('orm_default', $this->config['doctrine']['driver']);
        $this->assertIsArray($this->config['doctrine']['driver']['orm_default']);
        $this->assertArrayHasKey('drivers', $this->config['doctrine']['driver']['orm_default']);
        $this->assertIsArray($this->config['doctrine']['driver']['orm_default']);
    }

    public function testGetTemplates(): void
    {
        $this->assertArrayHasKey('paths', $this->config['templates']);
        $this->assertIsArray($this->config['templates']['paths']);
        $this->assertArrayHasKey('contact', $this->config['templates']['paths']);
    }
}
