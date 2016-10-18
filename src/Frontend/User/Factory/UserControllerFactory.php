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
use Dot\User\Mapper\UserMapperInterface;
use Dot\User\Service\UserServiceInterface;
use Dot\User\Validator\NoRecordsExists;
use Interop\Container\ContainerInterface;
use Zend\Validator\AbstractValidator;

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
        $userService = $container->get(UserServiceInterface::class);
        /** @var AbstractValidator $usernameValidator */
        $usernameValidator = new NoRecordsExists([
            'mapper' => $container->get(UserMapperInterface::class),
            'key' => 'username',
        ]);
        $usernameValidator->setMessage('Username is already registered and cannot be used');

        $controller = new UserController(
            $userService,
            $container->get(UserFormManager::class),
            $usernameValidator);

        return $controller;
    }
}