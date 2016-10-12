<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/6/2016
 * Time: 12:16 AM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Controller\UserController;
use Dot\Frontend\User\Service\UserServiceInterface;
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
        $controller = new UserController(
            $container->get(UserServiceInterface::class),
            $container->get(UserFormManager::class));

        return $controller;
    }
}