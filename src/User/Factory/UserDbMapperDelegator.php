<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 5:47 PM
 */

declare(strict_types = 1);

namespace App\User\Factory;

use App\User\Entity\UserDetailsEntity;
use App\User\Mapper\UserDbMapper;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class UserDbMapperDelegator
 * @package App\User\Factory
 */
class UserDbMapperDelegator implements DelegatorFactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $name
     * @param callable $callback
     * @param array|null $options
     * @return UserDbMapper
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        /** @var UserDbMapper $mapper */
        $mapper = $callback();

        /** @var HydratorPluginManager $hydratorManager */
        $hydratorManager = $container->get('HydratorManager');

        $userDetailsPrototype = new UserDetailsEntity();

        $mapper->setUserDetailsPrototype($userDetailsPrototype);
        $mapper->setUserDetailsHydrator($hydratorManager->get($userDetailsPrototype->hydrator()));

        return $mapper;
    }
}
