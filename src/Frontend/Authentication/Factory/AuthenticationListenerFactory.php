<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/17/2016
 * Time: 11:23 PM
 */

namespace Dot\Frontend\Authentication\Factory;

use Interop\Container\ContainerInterface;

/**
 * Class AuthenticationListenerFactory
 * @package Dot\Frontend\Authentication\Factory
 */
class AuthenticationListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationListener($container->get(UserDetailsMapperInterface::class));
    }
}