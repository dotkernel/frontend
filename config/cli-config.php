<?php

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;

$container = require __DIR__ . '/container.php';

$config = new PhpFile('config/doctrine_migrations.php');

$entityManager = $container->get('doctrine.entity_manager.orm_default');

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));
