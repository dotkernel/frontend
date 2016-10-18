<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Entity\UserDetailsEntity;
use Dot\Frontend\User\Entity\UserDetailsHydrator;
use Dot\Frontend\User\Mapper\UserDetailsDbMapper;
use Interop\Container\ContainerInterface;
use Zend\Db\ResultSet\HydratingResultSet;

/**
 * Class UserDetailsDbMapperFactory
 * @package Dot\Frontend\User\Factory
 */
class UserDetailsDbMapperFactory
{
    /**
     * @param ContainerInterface $container
     * @return UserDetailsDbMapper
     */
    public function __invoke(ContainerInterface $container)
    {
        $dbAdapter = $container->get('database');
        $resultSet = new HydratingResultSet(
            new UserDetailsHydrator(),
            new UserDetailsEntity()
        );

        $mapper = new UserDetailsDbMapper('user_details', $dbAdapter, null, $resultSet);
        return $mapper;
    }
}