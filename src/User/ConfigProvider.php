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

use App\User\Entity\UserEntity;
use App\User\Factory\UserFieldsetDelegator;
use App\User\Fieldset\UserDetailsFieldset;
use App\User\Form\AccountForm;
use App\User\Form\RegisterForm;
use App\User\Mapper\UserDbMapper;
use Dot\User\Factory\FormElementFactory;
use Dot\User\Factory\UserDbMapperFactory;
use Dot\User\Form\UserFieldset;
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

            'dot_user' => [
                'user_entity' => UserEntity::class,

                'event_listeners' => [
                    'user' => [

                    ],
                    'token' => [

                    ]
                ],

                'template_options' => [
                    'login_template' => 'user::login',
                    'register_template' => 'user::register',
                    'account_template' => 'user::account',
                    'change_password_template' => 'user::change-password',
                    'forgot_password_template' => 'user::forgot-password',
                    'reset_password_template' => 'user::reset-password',
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
}
