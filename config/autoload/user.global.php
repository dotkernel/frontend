<?php

return [

    'dependencies' => [
        //whatever dependencies you need additionally
        'factories' => [
            //event listeners for authentication and user events
            \Dot\Frontend\User\Listener\AuthenticationListener::class =>
                \Dot\Frontend\User\Factory\AuthenticationListenerFactory::class,

            \Dot\Frontend\User\Listener\UserEventsListener::class =>
                \Dot\Frontend\User\Factory\UserEventsListenerFactory::class,

            \Dot\Frontend\User\Listener\RegisterFormListener::class =>
                \Dot\Frontend\User\Factory\RegisterFormListenerFactory::class,

            //****************************
            //we overwrite the default user entity with this ones, to include details field
            \Dot\Frontend\User\Entity\UserEntity::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,

            \Dot\Frontend\User\Entity\UserEntityHydrator::class =>
                \Zend\ServiceManager\Factory\InvokableFactory::class,

            //********************************
            //extended user mapper and user details mapper
            \Dot\Frontend\User\Mapper\UserDetailsMapperInterface::class =>
                \Dot\Frontend\User\Factory\UserDetailsDbMapperFactory::class,

            //overwrite the user mapper service with our custom extended class
            \Dot\User\Mapper\UserMapperInterface::class =>
                \Dot\Frontend\User\Factory\UserDbMapperFactory::class,

            //overwrite the user service with our custom extended class
            \Dot\User\Service\UserServiceInterface::class =>
                \Dot\Frontend\User\Factory\UserServiceFactory::class,

            //overwrite user options class with the one we extended, to include new messages and template options
            \Dot\User\Options\UserOptions::class =>
                \Dot\Frontend\User\Factory\UserOptionsFactory::class
        ],

        'shared' => [
            \Dot\Frontend\User\Entity\UserEntity::class => false,
        ],
    ],

    'dot_user' => [
        //listeners for various user related events
        'user_event_listeners' => [
            \Dot\Frontend\User\Listener\UserEventsListener::class,
        ],

        //user entity and its hydrator to use for user transactions
        'user_entity' => \Dot\Frontend\User\Entity\UserEntity::class,
        'user_entity_hydrator' => \Dot\Frontend\User\Entity\UserEntityHydrator::class,

        //bcrypt cost, default to 11
        'password_cost' => 11,

        'enable_user_status' => true,

        //enable user form labes display
        'show_form_input_labels' => true,

        /**
         * Db options in case we use PDO/Mysql
         */
        'db_options' => [
            'db_adapter' => 'database',

            'user_table' => 'user',
            'user_reset_token_table' => 'user_reset_token',
            'user_confirm_token_table' => 'user_confirm_token',
        ],

        /**
         * These are options related to user registrations
         */
        'register_options' => [
            'enable_registration' => true,

            'enable_username' => true,

            'user_form_timeout' => 1800,

            'use_registration_form_captcha' => true,

            /*'form_captcha_options' => [
                'class'   => 'Figlet',
                'options' => [
                    'wordLen'    => 5,
                    'expiration' => 300,
                    'timeout'    => 300,
                ],
            ],*/

            'login_after_registration' => false,

            'default_user_status' => 'pending',
        ],

        'login_options' => [
            'login_form_timeout' => 1800,

            'enable_remember_me' => true,

            'remember_me_cookie_name' => 'rememberMe',

            'remember_me_cookie_expire' => 60 * 60 * 24 * 30,

            'remember_me_cookie_secure' => false,

            'auth_identity_fields' => ['username', 'email'],

            'allowed_login_statuses' => ['active'],
        ],

        'password_recovery_options' => [
            'enable_password_recovery' => true,

            'reset_password_token_timeout' => 3600,
        ],

        'confirm_account_options' => [
            'enable_account_confirmation' => true,

            'active_user_status' => 'active'
        ],

        'template_options' => [
            'change_password_template' => 'user::change-password',
        ],

        'form_manager' => [
            'factories' => [
                \Dot\Frontend\User\Form\UserForm::class =>
                    \Dot\Frontend\User\Factory\Form\UserFormFactory::class,
            ]
        ],

        'messages_options' => [
            'messages' => [
                \Dot\User\Options\MessagesOptions::MESSAGE_REGISTER_SUCCESS =>
                    'Account created. Check your email for confirmation'
            ]
        ],
    ],

    'dot_authentication' => [
        //this package specific configuration template
        'web' => [
            //template name to use for the login form
            //'login_template' => 'dot-user::login',

            //where to redirect after login success
            'after_login_route' => 'home',
            //where to redirect after logging out
            'after_logout_route' => 'login',
        ]
    ],

];