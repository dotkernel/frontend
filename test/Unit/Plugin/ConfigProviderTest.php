<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Plugin;

use Frontend\Plugin\ConfigProvider;
use Frontend\Plugin\FormsPlugin;
use Frontend\Plugin\PluginManager;
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

    public function testHasConfig(): void
    {
        $this->assertArrayHasKey('dot_controller', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['factories']);
        $this->assertArrayHasKey(PluginManager::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(FormsPlugin::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasInitializers(): void
    {
        $this->assertArrayHasKey('initializers', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['initializers']);
        $this->assertNotEmpty($this->config['dependencies']['initializers']);
    }

    public function testConfigHasPluginManager(): void
    {
        $this->assertArrayHasKey('plugin_manager', $this->config['dot_controller']);
        $this->assertIsArray($this->config['dot_controller']['plugin_manager']);
        $this->assertArrayHasKey('factories', $this->config['dot_controller']['plugin_manager']);
        $this->assertIsArray($this->config['dot_controller']['plugin_manager']['factories']);
        $this->assertArrayHasKey('forms', $this->config['dot_controller']['plugin_manager']['factories']);
        $this->assertNotEmpty($this->config['dot_controller']['plugin_manager']['factories']['forms']);
    }
}
