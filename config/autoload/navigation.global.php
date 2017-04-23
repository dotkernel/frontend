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
                                'route' => [
                                    'route_name' => 'user',
                                    'route_params' => ['action' => 'account']
                                ],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Change password',
                                'route' => [
                                    'route_name' => 'user',
                                    'route_params' => ['action' => 'change-password']
                                ],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Change email',
                                'route' => [
                                    'route_name' => 'user',
                                    'route_params' => ['action' => 'change-email']
                                ],
                            ],
                        ],
                        [
                            'options' => [
                                'label' => 'Delete account',
                                'route' => [
                                    'route_name' => 'user',
                                    'route_params' => ['action' => 'remove-account']
                                ],
                                'red-button' => true,
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
                                        'route' => [
                                            'route_name' => 'home',
                                        ],
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
                                        'route' => [
                                            'route_name' => 'page',
                                            'route_params' => ['action' => 'about-us']
                                        ],
                                        'icon' => 'fa fa-info-circle'
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Who we are',
                                        'route' => [
                                            'route_name' => 'page',
                                            'route_params' => ['action' => 'who-we-are']
                                        ],
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
                                        'label' => 'Protected content',
                                        'route' => [
                                            'route_name' => 'page',
                                            'route_params' => ['action' => 'premium-content']
                                        ],
                                        'icon' => 'fa fa-trophy'
                                    ]
                                ],
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Contact',
                                'route' => [
                                    'route_name' => 'contact',
                                    'options' => [
                                        'reuse_result_params' => false,
                                    ]
                                ],
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
                                        'route' => [
                                            'route_name' => 'user',
                                            'route_params' => ['action' => 'account']
                                        ],
                                        'icon' => '',
                                    ]
                                ],
                                [
                                    'options' => [
                                        'label' => 'Sign Out',
                                        'route' => [
                                            'route_name' => 'logout',
                                        ],
                                        'icon' => ''
                                    ]
                                ]
                            ]
                        ],
                        [
                            'options' => [
                                'label' => 'Login',
                                'route' => [
                                    'route_name' => 'login',
                                ],
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
