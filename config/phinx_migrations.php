<?php

// Load configuration
$container = require __DIR__ . '/container.php';
$config = $container->get('config');

$dbConfig = [
    'adapter' => 'mysql',
    'host' => $config['databases']['default']['host'],
    'name' => $config['databases']['default']['dbname'],
    'user' => $config['databases']['default']['user'],
    'pass' => $config['databases']['default']['password'],
    'port' => $config['databases']['default']['port'],
    'charset' => $config['databases']['default']['charset'],
];

return [
    'environments' => [
        'default_migration_table' => 'phinx_migrations',
        'default_database' => 'development',

        'production' => $dbConfig,
        'development' => $dbConfig,
        'testing' => $dbConfig,
    ],

    'paths' => [
        'migrations' => 'data/phinx/migrations',
        'seeds' =>  [
            'Data\\Database\\Seeds' => 'data/phinx/seeds'
        ]
    ],

    'foreign_keys' => false,

    'version_order' => 'creation',
];
