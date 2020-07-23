<?php

declare(strict_types=1);

namespace Frontend\User;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\User\Authentication\AuthenticationAdapter;
use Frontend\User\Authentication\AuthenticationAdapterFactory;
use Frontend\User\Authentication\AuthenticationServiceFactory;
use Frontend\User\Authentication\AuthenticationServiceInterface;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Form\LoginForm;
use Frontend\User\Controller\AccountController;
use Frontend\User\Controller\UserController;
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
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'dot_form' => $this->getForms(),
            'doctrine' => $this->getDoctrineConfig(),
        ];
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'factories'  => [
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
     * @return array
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

    public function getForms()
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

    public function getDoctrineConfig()
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
