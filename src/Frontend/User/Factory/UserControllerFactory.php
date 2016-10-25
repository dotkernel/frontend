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
use Dot\Frontend\User\Options\MessagesOptions;
use Dot\User\Form\UserFormManager;
use Dot\User\Mapper\UserMapperInterface;
use Dot\User\Options\UserOptions;
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
        /** @var \Dot\Frontend\User\Options\UserOptions $userOptions */
        $userOptions = $container->get(UserOptions::class);
        $userService = $container->get(UserServiceInterface::class);

        /** @var AbstractValidator $usernameValidator */
        $usernameValidator = new NoRecordsExists([
            'mapper' => $container->get(UserMapperInterface::class),
            'key' => 'username',
        ]);
        $usernameValidator->setMessage($userOptions->getMessagesOptions()
            ->getMessage(MessagesOptions::MESSAGE_REGISTER_USERNAME_ALREADY_REGISTERED));

        $controller = new UserController(
            $userService,
            $container->get(UserFormManager::class),
            $usernameValidator);

        return $controller;
    }
}