<?php

return [
    'debug' => false,
    'templates' => [
        'extension' => 'html.twig'
    ],
    'twig' => [
        'assets_url' => '/',
        'assets_version' => null,
        'autoescape' => 'html',
        'auto_reload' => true,
		'cache_dir' => 'data/cache/twig',
		'extensions' => [],
		'optimizations' => -1,
		'runtime_loaders' => [],
//		'timezone' => '',
        'globals' => [
            'appName' => $app['name']
        ],
	]
];
