<?php
/**
 * @see https://github.com/dotkernel/dot-frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Frontend\User;

use Dot\User\Factory\FormFactory;
use Dot\User\Factory\UserDbMapperFactory;
use Dot\User\Form\UserFieldset;
use Dot\User\Options\MessagesOptions;
use Frontend\User\Authentication\AuthenticationListener;
use Frontend\User\Controller\UserController;
use Frontend\User\Entity\UserEntity;
use Frontend\User\Factory\UserFieldsetDelegator;
use Frontend\User\Fieldset\UserDetailsFieldset;
use Frontend\User\Form\AccountForm;
use Frontend\User\Form\RegisterForm;
use Frontend\User\Listener\UserEventsListener;
use Frontend\User\Mapper\UserDbMapper;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider
 * @package App\User
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),

            'dot_form' => $this->getFormsConfig(),

            'dot_ems' => $this->getMappersConfig(),

            'dot_authentication' => $this->getAuthenticationConfig(),

            'routes' => $this->getRoutesConfig(),

            'dot_user' => [
                'user_entity' => UserEntity::class,

                //'route_default' => [],
                //'default_roles' => [],

                'event_listeners' => [
                    'user' => [
                        [
                            'type' => UserEventsListener::class,
                            'priority' => 100,
                        ]
                    ],
                    'token' => [
                        [
                            'type' => UserEventsListener::class,
                            'priority' => 100,
                        ]
                    ]
                ],

                'template_options' => [
                    'login_template' => 'user::login',
                    'register_template' => 'user::register',
                    'account_template' => 'user::account',
                    'change_password_template' => 'user::change-password',
                    'forgot_password_template' => 'user::forgot-password',
                    'reset_password_template' => 'user::reset-password',
                ],

                'messages_options' => [
                    'messages' => [
                        MessagesOptions::REGISTER_SUCCESS =>
                            'Your account was successfully created. Check your e-mail for account confirmation'
                    ]
                ]
            ]
        ];
    }

    public function getDependenciesConfig(): array
    {
        return [

        ];
    }

    public function getFormsConfig(): array
    {
        return [
            'form_manager' => [
                'factories' => [
                    UserDetailsFieldset::class => InvokableFactory::class,
                    RegisterForm::class => FormFactory::class,
                    AccountForm::class => FormFactory::class,
                ],
                'aliases' => [
                    'UserDetailsFieldset' => UserDetailsFieldset::class,
                    //overwrites some forms from dot-user
                    'Register' => RegisterForm::class,
                    'Account' => AccountForm::class,
                ],
                'delegators' => [
                    UserFieldset::class => [
                        UserFieldsetDelegator::class,
                    ]
                ]
            ]
        ];
    }

    public function getMappersConfig(): array
    {
        return [
            'mapper_manager' => [
                'factories' => [
                    UserDbMapper::class => UserDbMapperFactory::class,
                ],
                'aliases' => [
                    UserEntity::class => UserDbMapper::class,
                ],
            ]
        ];
    }

    public function getAuthenticationConfig(): array
    {
        return [
            'web' => [
                //'after_login_route' => [],

                'event_listeners' => [
                    [
                        'type' => AuthenticationListener::class,
                        'priority' => 100,
                    ]
                ]
            ]
        ];
    }

    public function getRoutesConfig(): array
    {
        return [
            'user_route' => [
                'middleware' => [UserController::class, \Dot\User\Controller\UserController::class],
            ]
        ];
    }
}
