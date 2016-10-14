<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 11:23 PM
 */

namespace Dot\Frontend\Factory;

use Dot\Frontend\Authentication\AuthenticationListener;
use Dot\Frontend\User\Mapper\UserDetailsMapperInterface;
use Interop\Container\ContainerInterface;

/**
 * Class AuthenticationListenerFactory
 * @package Dot\Frontend\Factory
 */
class AuthenticationListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return AuthenticationListener
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationListener($container->get(UserDetailsMapperInterface::class));
    }
}