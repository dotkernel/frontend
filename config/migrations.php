<?php

// Load configuration
$container = require __DIR__ . '/container.php';
$config = $container->get('config');

$dbConfig = [
    'adapter' => 'mysql',
    'host' => $config['databases'][ $config['databaseDefault'] ]['host'],
    'name' => $config['databases'][ $config['databaseDefault'] ]['dbname'],
    'user' => $config['databases'][ $config['databaseDefault'] ]['user'],
    'pass' => $config['databases'][ $config['databaseDefault'] ]['password'],
    'port' => $config['databases'][ $config['databaseDefault'] ]['port'],
    'charset' => $config['databases'][ $config['databaseDefault'] ]['charset'],
];

return [
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' =>  'development',

        'production' => $dbConfig,
        'development' => $dbConfig,
        'testing' => $dbConfig,
    ],

    'paths' => [
        'migrations' => 'data/database/migrations',
        'seeds' =>  [
            'Data\\Database\\Seeds' => 'data/database/seeds'
        ]
    ],

    'foreign_keys' => false,

    'version_order' => 'creation',
];
