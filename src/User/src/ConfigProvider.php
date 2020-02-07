<?php

declare(strict_types=1);

namespace Frontend\User;

use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\User\Entity\User;
use Frontend\User\Entity\UserInterface;
use Frontend\User\Handler\LoginHandler;
use Frontend\User\Handler\LogoutHandler;
use Frontend\User\Handler\RegisterHandler;

/**
 * Class ConfigProvider
 * @package Frontend\User
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * @return array
     */
    public function getDependencies() : array
    {
        return [
            'aliases' => [
                UserInterface::class => User::class
            ],
            'factories'  => [
                LoginHandler::class => AnnotatedServiceFactory::class,
                LogoutHandler::class => AnnotatedServiceFactory::class,
                RegisterHandler::class => AnnotatedServiceFactory::class,
            ]
        ];
    }

    /**
     * @return array
     */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'user' => [__DIR__ . '/../templates/user']
            ],
        ];
    }
}
