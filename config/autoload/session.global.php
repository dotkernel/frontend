<?php

return [
    'dot_flashmessenger' => [
        'options' => [
            'namespace' => 'frontend_messenger'
        ]
    ],
    'dot_session' => [
        'cookieName' => 'remember_me_token',
        'rememberMeInactive' => 1800,
    ],
    'session_config' => [
        'name' => 'FRONTEND_SESSID',
    ],
    'session_containers' => [
        'user'
    ]
];
