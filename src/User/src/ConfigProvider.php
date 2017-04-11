<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User;

use Dot\User\Factory\FormFactory;
use Dot\User\Factory\UserDbMapperFactory;
use Dot\User\Form\UserFieldset;
use Dot\User\Options\MessagesOptions;
use Frontend\User\Authentication\AuthenticationListener;
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
 * @package Frontend\User
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),

            'dot_form' => $this->getForms(),

            'dot_mapper' => $this->getMappers(),

            'templates' => $this->getTemplates(),

            'dot_authentication' => $this->getAuthentication(),

            'dot_user' => [
                'user_entity' => UserEntity::class,

                'route_default' => [
                    'route_name' => 'user',
                    'route_params' => ['action' => 'account']
                ],
                'default_roles' => ['user'],

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
                            'Account was successfully created. Check your e-mail for account activation'
                    ]
                ]
            ]
        ];
    }

    public function getDependencies(): array
    {
        return [

        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'user' => [__DIR__ . '/../templates/user']
            ]
        ];
    }

    public function getForms(): array
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

    public function getMappers(): array
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

    public function getAuthentication(): array
    {
        return [
            'web' => [
                'login_route' => [
                    'route_name' => 'login',
                ],

                'logout_route' => [
                    'route_name' => 'logout',
                ],

                'after_logout_route' => [
                    'route_name' => 'login',
                ],

                'after_login_route' => [
                    'route_name' => 'user',
                    'route_params' => ['action' => 'account']
                ],

                'event_listeners' => [
                    [
                        'type' => AuthenticationListener::class,
                        'priority' => 100,
                    ]
                ]
            ]
        ];
    }
}
