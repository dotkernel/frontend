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

            \Dot\Frontend\User\Form\UserDetailsFieldset::class =>
                \Dot\Frontend\User\Factory\Form\UserDetailsFieldsetFactory::class,

            \Dot\Frontend\User\Form\InputFilter\UserDetailsInputFilter::class =>
                \Dot\Frontend\User\Factory\Form\UserDetailsInputFilterFactory::class,

            \Dot\Frontend\User\Service\UserService::class =>
                \Dot\User\Factory\UserServiceFactory::class,
        ],

        'delegators' => [
            \Dot\User\Mapper\UserDbMapper::class => [
                \Dot\Frontend\User\Factory\UserMapperDelegator::class,
            ]
        ],

        'aliases' => [
            'UserService' => \Dot\Frontend\User\Service\UserService::class
        ],
    ],

    'dot_user' => [
        //listeners for various user related events
        'user_event_listeners' => [
            \Dot\Frontend\User\Listener\UserEventsListener::class,
        ],

        'user_entity' => \Dot\Frontend\User\Entity\UserEntity::class,

        'enable_user_status' => true,

        'show_form_input_labels' => true,

        'form_csrf_timeout' => 3600,

        'db_options' => [
            'db_adapter' => 'database',

            'user_table' => 'user',
            'user_reset_token_table' => 'user_reset_token',
            'user_confirm_token_table' => 'user_confirm_token',
        ],

        'register_options' => [
            'enable_registration' => true,

            'enable_username' => true,

            'use_registration_form_captcha' => true,

            'login_after_registration' => false,

            'default_user_status' => 'pending',

            //see dot-user documentation for more options...
        ],

        'login_options' => [

            'enable_remember_me' => true,

            'remember_me_cookie_secure' => false,

            'auth_identity_fields' => ['username', 'email'],

            'allowed_login_statuses' => ['active'],

            //see dot-user documentation for more options...
        ],

        'password_recovery_options' => [
            'enable_password_recovery' => true,

            'reset_password_token_timeout' => 3600,

            //see dot-user documentation for more options...
        ],

        'confirm_account_options' => [
            'enable_account_confirmation' => true,

            'active_user_status' => 'active'

            //see dot-user documentation for more options...
        ],

        'template_options' => [
            'account_template_layout' => '@layout/sidemenu.html.twig',
            'change_password_template_layout' => '@layout/sidemenu.html.twig',
            'login_template_layout' => '@layout/single-form.html.twig',
            'register_template_layout' => '@layout/single-form.html.twig',

            //see dot-user documentation for more options...
        ],

        'form_manager' => [
            'delegators' => [
                \Dot\User\Form\UserForm::class => [
                    \Dot\Frontend\User\Factory\Form\UserFormDelegator::class,
                ]
            ],
        ],

        'messages_options' => [
            'messages' => [
                \Dot\User\Options\MessagesOptions::MESSAGE_REGISTER_SUCCESS =>
                    'Account created. Check your email for confirmation'
            ]

            //see dot-user documentation for more options...
        ],
    ],

    'dot_authentication' => [
        //this package specific configuration template
        'web' => [
            //where to redirect after login success
            'after_login_route' => 'home',
            //where to redirect after logging out
            'after_logout_route' => 'login',
        ]
    ],

];