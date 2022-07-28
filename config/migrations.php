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

// As of doctrine/migrations 3.5.1, setting a connection name will throw a "No multiple connections" exception
// as seen in Doctrine\Migrations\Configuration\Connection\ExistingConnection::21; set it to null or remove line 28 altogether

    'doctrine_configuration' => [
        'migrations_directory_namespace' => 'DotKernel4\Frontend\Migrations',
        'migrations_directory' => 'data/doctrine/migrations',
        'table_name' => 'doctrine_migration_versions',
        'column_name' => 'version',
        'column_length' => 100,
        'executed_at_column_name' => 'executedAt',
        'all_or_nothing' => true,
        'check_database_platform' => false,
        'connection_name' => null
    ]
];
