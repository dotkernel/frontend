<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Ems\Validator\NoRecordsExists;
use Dot\Frontend\User\Controller\UserController;
use Dot\User\Form\UserFormManager;
use Dot\User\Options\UserOptions;
use Dot\User\Service\UserServiceInterface;
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
        /** @var UserOptions $userOptions */
        $userOptions = $container->get(UserOptions::class);
        $userService = $container->get(UserServiceInterface::class);

        /** @var AbstractValidator $usernameValidator */
        $usernameValidator = new NoRecordsExists([
            'service' => $userService,
            'key' => 'username',
        ]);
        $usernameValidator->setMessage('Requested username is already taken. Please choose another one');

        $controller = new UserController(
            $userService,
            $container->get(UserFormManager::class),
            $usernameValidator);

        return $controller;
    }
}