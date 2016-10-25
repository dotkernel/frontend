<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 10/25/2016
 * Time: 8:11 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class UserOptionsFactory
 * @package Dot\Frontend\User\Factory
 */
class UserOptionsFactory
{
    /**
     * @param ContainerInterface $container
     * @return UserOptions
     */
    public function __invoke(ContainerInterface $container)
    {
        return new UserOptions($container->get('config')['dot_user']);
    }
}