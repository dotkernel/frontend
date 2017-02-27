<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 1:30 AM
 */

declare(strict_types = 1);

namespace App\User;

use App\User\Authentication\AuthenticationListener;
use App\User\Controller\UserController;
use App\User\Entity\UserEntity;
use App\User\Factory\UserFieldsetDelegator;
use App\User\Fieldset\UserDetailsFieldset;
use App\User\Form\AccountForm;
use App\User\Form\RegisterForm;
use App\User\Listener\UserEventsListener;
use App\User\Mapper\UserDbMapper;
use Dot\User\Factory\FormElementFactory;
use Dot\User\Factory\UserDbMapperFactory;
use Dot\User\Form\UserFieldset;
use Dot\User\Options\MessagesOptions;
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
                    RegisterForm::class => FormElementFactory::class,
                    AccountForm::class => FormElementFactory::class,
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
