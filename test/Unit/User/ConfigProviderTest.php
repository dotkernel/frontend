<?php

declare(strict_types=1);

namespace FrontendTest\Unit\User;

use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\ConfigProvider;
use Frontend\User\Controller\AccountController;
use Frontend\User\Controller\UserController;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Repository\UserRoleRepository;
use Frontend\User\RoutesDelegator;
use Frontend\User\Service\UserRoleService;
use Frontend\User\Service\UserRoleServiceInterface;
use Frontend\User\Service\UserService;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Authentication\AuthenticationService;
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

    public function testConfigHasTemplates(): void
    {
        $this->assertArrayHasKey('templates', $this->config);
    }

    public function testConfigHasForms(): void
    {
        $this->assertArrayHasKey('forms', $this->config);
    }

    public function testConfigHasDoctrineConfig(): void
    {
        $this->assertArrayHasKey('doctrine', $this->config);
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
        $this->assertArrayHasKey(AuthenticationService::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(AuthenticationAdapter::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(UserController::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(AccountController::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(UserService::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(UserRoleService::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(UserRepository::class, $this->config['dependencies']['factories']);
        $this->assertArrayHasKey(UserRoleRepository::class, $this->config['dependencies']['factories']);
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);
        $this->assertIsArray($this->config['dependencies']['aliases']);
        $this->assertArrayHasKey(UserInterface::class, $this->config['dependencies']['aliases']);
        $this->assertArrayHasKey(UserServiceInterface::class, $this->config['dependencies']['aliases']);
        $this->assertArrayHasKey(UserRoleServiceInterface::class, $this->config['dependencies']['aliases']);
    }

    public function testGetTemplates(): void
    {
        $this->assertArrayHasKey('paths', $this->config['templates']);
        $this->assertIsArray($this->config['templates']['paths']);
        $this->assertArrayHasKey('user', $this->config['templates']['paths']);
        $this->assertArrayHasKey('profile', $this->config['templates']['paths']);
    }

    public function testGetForms(): void
    {
        $this->assertArrayHasKey('form_manager', $this->config['forms']);
        $this->assertIsArray($this->config['forms']['form_manager']);
        $this->assertArrayHasKey('factories', $this->config['forms']['form_manager']);
        $this->assertArrayHasKey('aliases', $this->config['forms']['form_manager']);
        $this->assertArrayHasKey('delegators', $this->config['forms']['form_manager']);
    }

    public function testGetDoctrineConfig(): void
    {
        $this->assertArrayHasKey('driver', $this->config['doctrine']);
        $this->assertIsArray($this->config['doctrine']['driver']);
    }
}
