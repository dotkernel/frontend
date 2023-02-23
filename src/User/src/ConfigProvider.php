<?php

declare(strict_types=1);

namespace Frontend\User;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\User\Adapter\AuthenticationAdapter;
use Frontend\User\Controller\AccountController;
use Frontend\User\Controller\UserController;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Factory\AuthenticationAdapterFactory;
use Frontend\User\Factory\AuthenticationServiceFactory;
use Frontend\User\Form\LoginForm;
use Frontend\User\Service\UserRoleService;
use Frontend\User\Service\UserRoleServiceInterface;
use Frontend\User\Service\UserService;
use Frontend\User\Service\UserServiceInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Form\ElementFactory;

/**
 * Class ConfigProvider
 * @package Frontend\User
 */
final class ConfigProvider
{
    /**
     * @return array{dependencies: mixed[], templates: mixed[], dot_form: mixed[], doctrine: mixed[]}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
            'dot_form' => $this->getForms(),
            'doctrine' => $this->getDoctrineConfig(),
        ];
    }

    /**
     * @return array{factories: array<string, class-string<\Dot\AnnotatedServices\Factory\AnnotatedServiceFactory>|class-string<\Frontend\User\Factory\AuthenticationServiceFactory>|class-string<\Frontend\User\Factory\AuthenticationAdapterFactory>>, aliases: array<string, class-string<\Frontend\User\Entity\User>|class-string<\Frontend\User\Service\UserService>|class-string<\Frontend\User\Service\UserRoleService>>}
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                UserController::class => AnnotatedServiceFactory::class,
                AccountController::class => AnnotatedServiceFactory::class,
                UserService::class => AnnotatedServiceFactory::class,
                UserRoleService::class => AnnotatedServiceFactory::class,
                AuthenticationService::class => AuthenticationServiceFactory::class,
                AuthenticationAdapter::class => AuthenticationAdapterFactory::class,
            ],
            'aliases' => [
                UserInterface::class => User::class,
                UserServiceInterface::class => UserService::class,
                UserRoleServiceInterface::class => UserRoleService::class,
            ]
        ];
    }

    /**
     * @return array{paths: array{user: string[], profile: string[]}}
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'user' => [__DIR__ . '/../templates/user'],
                'profile' => [__DIR__ . '/../templates/profile']
            ],
        ];
    }

    /**
     * @return array{form_manager: array{factories: array<string, class-string<\Laminas\Form\ElementFactory>>, aliases: never[], delegators: never[]}}
     */
    public function getForms(): array
    {
        return [
            'form_manager' => [
                'factories' => [
                    LoginForm::class => ElementFactory::class,
                ],
                'aliases' => [
                ],
                'delegators' => [
                ]
            ],
        ];
    }

    /**
     * @return array{driver: array{orm_default: array{drivers: array{Frontend\User\Entity: string}}, UserEntities: array{class: class-string<\Doctrine\ORM\Mapping\Driver\AnnotationDriver>, cache: string, paths: string[]}}}
     */
    public function getDoctrineConfig(): array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'Frontend\User\Entity' => 'UserEntities',
                    ]
                ],
                'UserEntities' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ]
            ]
        ];
    }
}
