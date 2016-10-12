<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/2/2016
 * Time: 7:56 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Mapper\UserDbMapper;
use Dot\Frontend\User\Mapper\UserDetailsMapperInterface;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Db\ResultSet\HydratingResultSet;

/**
 * Class UserDbMapperFactory
 * @package Dot\Frontend\User\Factory
 */
class UserDbMapperFactory
{
    /**
     * @param ContainerInterface $container
     * @return UserDbMapper
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $dbAdapter = $container->get($options->getDbOptions()->getDbAdapter());

        $resultSetPrototype = new HydratingResultSet(
            $container->get($options->getUserEntityHydrator()),
            $container->get($options->getUserEntity()));

        $mapper = new UserDbMapper(
            $options->getDbOptions()->getUserTable(),
            $options->getDbOptions(),
            $dbAdapter,
            null,
            $resultSetPrototype);
        
        $mapper->setUserDetailsMapper($container->get(UserDetailsMapperInterface::class));

        return $mapper;
    }
}