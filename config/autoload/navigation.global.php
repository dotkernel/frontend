<?php

return [
    'dot_navigation' => [
        //enable menu item active if any child is active
        'active_recursion' => true,

        'containers' => [
            'account_side_menu' => [
                'type' => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options' => [
                                'label' => 'Personal Information',
                                'route' => 'user',
                                'params' => ['action' => 'account'],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Change password',
                                'route' => 'user',
                                'params' => ['action' => 'change-password'],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Change email',
                                'route' => 'user',
                                'params' => ['action' => 'change-email'],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Delete account',
                                'route' => 'user',
                                'red-button' => true,
                                'params' => ['action' => 'remove-account'],
                            ],
                        ],
                    ],
                ],
            ],

            'main_menu' => [
                'type' => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options' => [
                                'label' => 'Pages',
                                'uri' => '#',
                                'icon' => '',
                            ],
                            'pages' => [
                                [
                                    'options' => [
                                        'label' => 'Home',
                                        'route' => 'home',
                                        'icon' => 'fa fa-home'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'separator',
                                        'type' => 'separator',
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'About Us',
                                        'route' => 'page',
                                        'params' => ['action' => 'about-us'],
                                        'icon' => 'fa fa-info-circle'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Who we are',
                                        'route' => 'page',
                                        'params' => ['action' => 'who-we-are'],
                                        'icon' => 'fa fa-copyright'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'separator',
                                        'type' => 'separator',
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Premium Content',
                                        'route' => 'page',
                                        'params' => ['action' => 'premium-content'],
                                        'icon' => 'fa fa-trophy'
                                    ]
                                ],
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Contact',
                                'route' => 'contact',
                                'params' => ['action' => ''],
                                'icon' => '',
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Contribute',
                                'uri' => 'https://github.com/dotkernel',
                                'icon' => '',
                            ],
                            'attributes' => [
                                'target' => '_blank'
                            ],
                        ],
                    ],
                ],
            ],

            'account_menu' => [
                'type' => 'ArrayProvider',
                'options' => [
                    'items' => [
                        [
                            'options' => [
                                'label' => 'Welcome, ',
                                'id' => 'account',
                                'uri' => '#',
                                'icon' => '',
                                'permission' => 'authenticated'
                            ],
                            'attributes' => [
                                'class' => 'navbar-colored-item user-menu-icon',
                            ],
                            'pages' => [
                                [
                                    'options' => [
                                        'label' => 'Settings',
                                        'route' => 'user',
                                        'params' => ['action' => 'account'],
                                        'icon' => '',
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Sign Out',
                                        'route' => 'logout',
                                        'icon' => ''
                                    ]
                                ]
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Login',
                                'route' => 'login',
                                'icon' => '',
                                'permission' => 'unauthenticated'
                            ],
                            'attributes' => [
                                'class' => 'navbar-colored-item user-menu-icon',
                            ]
                        ],
                    ],
                ],
            ],
        ],

        //register custom providers here
        'provider_manager' => []
    ]
];
