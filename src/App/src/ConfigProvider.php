<?php

declare(strict_types=1);

namespace Frontend\App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\App\Controller\LanguageController;
use Frontend\App\Factory\EntityListenerResolverFactory;
use Frontend\App\Resolver\EntityListenerResolver;
use Frontend\App\Service\CookieService;
use Frontend\App\Service\CookieServiceInterface;
use Frontend\App\Service\RecaptchaService;
use Frontend\App\Service\TranslateService;
use Frontend\App\Service\TranslateServiceInterface;
use Mezzio\Application;
use Roave\PsrContainerDoctrine\EntityManagerFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
final class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array{dependencies: mixed[], doctrine: mixed[], templates: mixed[]}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine' => $this->getDoctrineConfig(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     * @return array{delegators: array<string, array<class-string<\Frontend\App\RoutesDelegator>|class-string<\Frontend\Page\RoutesDelegator>|class-string<\Frontend\User\RoutesDelegator>>>, factories: array<string, class-string<\Roave\PsrContainerDoctrine\EntityManagerFactory>|class-string<\Frontend\App\Factory\EntityListenerResolverFactory>|class-string<\Dot\AnnotatedServices\Factory\AnnotatedServiceFactory>>, aliases: array<string, class-string<\Frontend\App\Service\TranslateService>|class-string<\Frontend\App\Service\CookieService>|string>}
     */
    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                    \Frontend\Page\RoutesDelegator::class,
                    \Frontend\User\RoutesDelegator::class
                ]
            ],
            'factories' => [
                'doctrine.entity_manager.orm_default' => EntityManagerFactory::class,
                EntityListenerResolver::class => EntityListenerResolverFactory::class,
                TranslateService::class => AnnotatedServiceFactory::class,
                LanguageController::class => AnnotatedServiceFactory::class,
                RecaptchaService::class => AnnotatedServiceFactory::class,
                CookieService::class => AnnotatedServiceFactory::class,
            ],
            'aliases' => [
                EntityManager::class => 'doctrine.entity_manager.orm_default',
                EntityManagerInterface::class => 'doctrine.entity_manager.default',
                TranslateServiceInterface::class => TranslateService::class,
                CookieServiceInterface::class => CookieService::class,
            ]
        ];
    }

    /**
     * @return array{configuration: array{orm_default: array{entity_listener_resolver: class-string<\Frontend\App\Resolver\EntityListenerResolver>}}}
     */
    public function getDoctrineConfig(): array
    {
        return [
            'configuration' => [
                'orm_default' => [
                    'entity_listener_resolver' => EntityListenerResolver::class,
                ]
            ]
        ];
    }

    /**
     * Returns the templates configuration
     * @return array{paths: array<string, string[]>}
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => [__DIR__ . '/../templates/app'],
                'error' => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
                'partial' => [__DIR__ . '/../templates/partial'],
                'language' => [__DIR__ . '/../templates/language'],
            ],
        ];
    }
}
