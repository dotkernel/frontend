<?php

declare(strict_types=1);

namespace Frontend\User;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Dot\AnnotatedServices\Factory\AnnotatedRepositoryFactory;
use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\Controller\AccountController;
use Frontend\User\Controller\UserController;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Factory\AuthenticationAdapterFactory;
use Frontend\User\Factory\AuthenticationServiceFactory;
use Frontend\User\Form\LoginForm;
use Frontend\User\Repository\UserRepository;
use Frontend\User\Repository\UserRoleRepository;
use Frontend\User\Service\UserRoleService;
use Frontend\User\Service\UserRoleServiceInterface;
use Frontend\User\Service\UserService;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Form\ElementFactory;
use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'forms'        => $this->getForms(),
            'doctrine'     => $this->getDoctrineConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                AuthenticationService::class => AuthenticationServiceFactory::class,
                AuthenticationAdapter::class => AuthenticationAdapterFactory::class,
                UserController::class        => AnnotatedServiceFactory::class,
                AccountController::class     => AnnotatedServiceFactory::class,
                UserService::class           => AnnotatedServiceFactory::class,
                UserRoleService::class       => AnnotatedServiceFactory::class,
                UserRepository::class        => AnnotatedRepositoryFactory::class,
                UserRoleRepository::class    => AnnotatedRepositoryFactory::class,
            ],
            'aliases'    => [
                UserInterface::class            => User::class,
                UserServiceInterface::class     => UserService::class,
                UserRoleServiceInterface::class => UserRoleService::class,
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'user'    => [__DIR__ . '/../templates/user'],
                'profile' => [__DIR__ . '/../templates/profile'],
            ],
        ];
    }

    public function getForms(): array
    {
        return [
            'form_manager' => [
                'factories'  => [
                    LoginForm::class => ElementFactory::class,
                ],
                'aliases'    => [],
                'delegators' => [],
            ],
        ];
    }

    public function getDoctrineConfig(): array
    {
        return [
            'driver' => [
                'orm_default'  => [
                    'drivers' => [
                        'Frontend\User\Entity' => 'UserEntities',
                    ],
                ],
                'UserEntities' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ],
            ],
        ];
    }
}
