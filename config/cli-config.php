<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/container.php';
$migrations = require __DIR__ . '/migrations.php';
$config = $migrations['doctrine_configuration'];

try {
    $connection = DriverManager::getConnection($container->get('config')['databases']['default'] ?? null);
} catch (Exception $exception) {
    exit($exception->getMessage());
}

$configuration = new Configuration();
$configuration->addMigrationsDirectory($config['migrations_directory_namespace'], $config['migrations_directory']);
$configuration->setAllOrNothing($config['all_or_nothing']);
$configuration->setCheckDatabasePlatform($config['check_database_platform']);
$configuration->setConnectionName($config['connection_name']);

$storageConfiguration = new TableMetadataStorageConfiguration();
$storageConfiguration->setTableName($config['table_name']);
$storageConfiguration->setVersionColumnName($config['column_name']);
$storageConfiguration->setVersionColumnLength($config['column_length']);
$storageConfiguration->setExecutedAtColumnName($config['executed_at_column_name']);

$configuration->setMetadataStorageConfiguration($storageConfiguration);

$dependencyFactory = DependencyFactory::fromConnection(
    new ExistingConfiguration($configuration),
    new ExistingConnection($connection)
);
$cli = new Application('Doctrine Migrations');
$cli->setCatchExceptions(true);

$cli->addCommands(array(
    new Command\DumpSchemaCommand($dependencyFactory),
    new Command\ExecuteCommand($dependencyFactory),
    new Command\GenerateCommand($dependencyFactory),
    new Command\LatestCommand($dependencyFactory),
    new Command\ListCommand($dependencyFactory),
    new Command\MigrateCommand($dependencyFactory),
    new Command\RollupCommand($dependencyFactory),
    new Command\StatusCommand($dependencyFactory),
    new Command\SyncMetadataCommand($dependencyFactory),
    new Command\VersionCommand($dependencyFactory),
));

$cli->run();
