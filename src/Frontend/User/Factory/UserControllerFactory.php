<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Controller\UserController;
use Dot\User\Form\UserFormManager;
use Interop\Container\ContainerInterface;

/**
 * Class UserControllerFactory
 * @package Dot\Frontend\User\Factory
 */
class UserControllerFactory
{
    /**
     * @param ContainerInterface $container
     * @return UserController
     */
    public function __invoke(ContainerInterface $container)
    {
        $userService = $container->get('UserService');

        $controller = new UserController(
            $userService,
            $container->get(UserFormManager::class));

        return $controller;
    }
}