<?php

declare(strict_types=1);

return [
    'dot_navigation' => [
        //enable menu item active if any child is active
        'active_recursion' => true,
        'containers'       => [
            'left_menu'         => [
                'type'    => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options'    => [
                                'label' => 'Pages',
                                'route' => [],
                            ],
                            'attributes' => [
                                'class' => 'nav-link dropdown-toggle',
                                'href'  => '#',
                            ],
                            'pages'      => [
                                [
                                    'options'    => [
                                        'label' => 'Home',
                                        'uri'   => '/home',
                                    ],
                                    'attributes' => [
                                        'class' => 'dropdown-item',
                                    ],
                                ],
                                [
                                    'options'    => [
                                        'label' => 'About Us',
                                        'uri'   => '/page/about-us',
                                    ],
                                    'attributes' => [
                                        'class' => 'dropdown-item',
                                    ],
                                ],
                                [
                                    'options'    => [
                                        'label' => 'Who We Are',
                                        'uri'   => '/page/who-we-are',
                                    ],
                                    'attributes' => [
                                        'class' => 'dropdown-item',
                                    ],
                                ],
                                [
                                    'options'    => [
                                        'label' => 'Premium content',
                                        'uri'   => '/page/premium-content',
                                    ],
                                    'attributes' => [
                                        'class' => 'dropdown-item',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'options'    => [
                                'label' => 'Contribute',
                                'uri'   => 'https://github.com/dotkernel',
                            ],
                            'attributes' => [
                                'class'  => 'nav-link',
                                'target' => '_blank',
                            ],
                        ],
                        [
                            'options'    => [
                                'label' => 'Contact Us',
                                'uri'   => '/contact/form',
                            ],
                            'attributes' => [
                                'class' => 'nav-link',
                            ],
                        ],
                        [
                            'options'    => [
                                'label' => 'Disabled',
                                'uri'   => '/',
                            ],
                            'attributes' => [
                                'class' => 'nav-link disabled',
                            ],
                        ],
                    ],
                ],
            ],
            'guest_menu'        => [
                'type'    => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options'    => [
                                'label' => 'Log in',
                                'uri'   => '/user/login',
                            ],
                            'attributes' => [
                                'class' => 'nav-link',
                            ],
                        ],
                    ],
                ],
            ],
            'user_menu'         => [
                'type'    => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options'    => [
                                'label' => 'Profile',
                                'uri'   => '/account/details',
                            ],
                            'attributes' => [
                                'class' => 'nav-link',
                            ],
                        ],
                        [
                            'options'    => [
                                'label' => 'Log out',
                                'uri'   => '/user/logout',
                            ],
                            'attributes' => [
                                'class' => 'nav-link',
                            ],
                        ],
                    ],
                ],
            ],
            'user_profile_menu' => [
                'type'    => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options' => [
                                'label' => 'Avatar',
                                'uri'   => '/account/avatar',
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Details',
                                'uri'   => '/account/details',
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Change password',
                                'uri'   => '/account/change-password',
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Delete account',
                                'uri'   => '/account/delete-account',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        //register custom providers here
        'provider_manager' => [],
    ],
];
