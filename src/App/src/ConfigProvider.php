<?php

declare(strict_types=1);

namespace Frontend\App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dot\DependencyInjection\Factory\AttributedServiceFactory;
use Frontend\App\Controller\LanguageController;
use Frontend\App\Factory\EntityListenerResolverFactory;
use Frontend\App\Middleware\RememberMeMiddleware;
use Frontend\App\Middleware\TranslatorMiddleware;
use Frontend\App\Resolver\EntityListenerResolver;
use Frontend\App\Service\CookieService;
use Frontend\App\Service\CookieServiceInterface;
use Frontend\App\Service\RecaptchaService;
use Frontend\App\Service\TranslateService;
use Frontend\App\Service\TranslateServiceInterface;
use Mezzio\Application;
use Roave\PsrContainerDoctrine\EntityManagerFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine'     => $this->getDoctrineConfig(),
            'templates'    => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                'doctrine.entity_manager.orm_default' => EntityManagerFactory::class,
                EntityListenerResolver::class         => EntityListenerResolverFactory::class,
                TranslateService::class               => AttributedServiceFactory::class,
                LanguageController::class             => AttributedServiceFactory::class,
                RecaptchaService::class               => AttributedServiceFactory::class,
                CookieService::class                  => AttributedServiceFactory::class,
                TranslatorMiddleware::class           => AttributedServiceFactory::class,
                RememberMeMiddleware::class           => AttributedServiceFactory::class,
            ],
            'aliases'    => [
                EntityManager::class             => 'doctrine.entity_manager.orm_default',
                EntityManagerInterface::class    => 'doctrine.entity_manager.orm_default',
                TranslateServiceInterface::class => TranslateService::class,
                CookieServiceInterface::class    => CookieService::class,
            ],
        ];
    }

    public function getDoctrineConfig(): array
    {
        return [
            'configuration' => [
                'orm_default' => [
                    'entity_listener_resolver' => EntityListenerResolver::class,
                ],
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'      => [__DIR__ . '/../templates/app'],
                'error'    => [__DIR__ . '/../templates/error'],
                'layout'   => [__DIR__ . '/../templates/layout'],
                'partial'  => [__DIR__ . '/../templates/partial'],
                'language' => [__DIR__ . '/../templates/language'],
            ],
        ];
    }
}
