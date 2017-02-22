<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 1/24/2017
 * Time: 9:19 PM
 */

return [

    'dot_hydrator' => [
        'hydrator_manager' => [
            'factories' => [
                \Dot\User\Entity\UserEntityHydrator::class =>
                    \Zend\ServiceManager\Factory\InvokableFactory::class,
            ]
        ],
    ],

    'dot_input_filter' => [
        'input_filter_manager' => [
            'factories' => [

            ],
        ],
    ],

    'dot_form' => [
        'form_manager' => [
            'factories' => [

            ],
        ],

        'forms' => [

        ],
    ],
];
