<?php

use Frontend\Console\Command\HelloCommand;

return [
    'dot_console' => [
        //'name' => 'DotKernel Console',
        //'version' => '1.0.0',

        'commands' => [
            [
                'name' => 'hello',
                'description' => 'Hello, World! command full description',
                'short_description' => 'Hello, World! command short description',
                'handler' => HelloCommand::class,
            ],
        ]
    ]
];
