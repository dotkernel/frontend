<?php

declare(strict_types=1);

namespace Frontend\Contact;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\Contact\Controller\ContactController;
use Frontend\Contact\Form\ContactForm;
use Frontend\Contact\Service\MessageService;
use Frontend\Contact\Service\MessageServiceInterface;
use Laminas\Form\ElementFactory;
use Mezzio\Application;

/**
 * Class ConfigProvider
 * @package Frontend\Contact
 */
final class ConfigProvider
{
    /**
     * @return array{dependencies: mixed[], templates: mixed[], dot_form: mixed[], doctrine: mixed[]}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
            'dot_form' => $this->getForms(),
            'doctrine' => $this->getDoctrineConfig()
        ];
    }

    /**
     * @return array{delegators: array<string, array<class-string<\Frontend\Contact\RoutesDelegator>>>, factories: array<string, class-string<\Dot\AnnotatedServices\Factory\AnnotatedServiceFactory>>, aliases: array<string, class-string<\Frontend\Contact\Service\MessageService>>}
     */
    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories' => [
                ContactController::class => AnnotatedServiceFactory::class,
                MessageService::class => AnnotatedServiceFactory::class,
            ],
            'aliases' => [
                MessageServiceInterface::class => MessageService::class,
            ]
        ];
    }

    /**
     * @return array{paths: array{contact: string[]}}
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'contact' => [__DIR__ . '/../templates/contact']
            ],
        ];
    }

    /**
     * @return array{form_manager: array{factories: array<string, class-string<\Laminas\Form\ElementFactory>>, aliases: never[]}}
     */
    public function getForms(): array
    {
        return [
            'form_manager' => [
                'factories' => [
                    ContactForm::class => ElementFactory::class,
                ],
                'aliases' => [
                ],
            ],
        ];
    }

    /**
     * @return array{driver: array{orm_default: array{drivers: array{Frontend\Contact\Entity: string}}, ContactEntities: array{class: class-string<\Doctrine\ORM\Mapping\Driver\AnnotationDriver>, cache: string, paths: string[]}}}
     */
    public function getDoctrineConfig(): array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'Frontend\Contact\Entity' => 'ContactEntities',
                    ]
                ],
                'ContactEntities' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ]
            ]
        ];
    }
}
