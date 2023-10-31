<?php

declare(strict_types=1);

namespace Frontend\Contact;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Dot\AnnotatedServices\Factory\AnnotatedRepositoryFactory;
use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\Contact\Controller\ContactController;
use Frontend\Contact\Form\ContactForm;
use Frontend\Contact\Repository\MessageRepository;
use Frontend\Contact\Service\MessageService;
use Frontend\Contact\Service\MessageServiceInterface;
use Laminas\Form\ElementFactory;
use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'forms'        => $this->getForms(),
            'doctrine'     => $this->getDoctrineConfig(),
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
                ContactController::class => AnnotatedServiceFactory::class,
                MessageService::class    => AnnotatedServiceFactory::class,
                MessageRepository::class => AnnotatedRepositoryFactory::class,
            ],
            'aliases'    => [
                MessageServiceInterface::class => MessageService::class,
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'contact' => [__DIR__ . '/../templates/contact'],
            ],
        ];
    }

    public function getForms(): array
    {
        return [
            'form_manager' => [
                'factories' => [
                    ContactForm::class => ElementFactory::class,
                ],
                'aliases'   => [],
            ],
        ];
    }

    public function getDoctrineConfig(): array
    {
        return [
            'driver' => [
                'orm_default'     => [
                    'drivers' => [
                        'Frontend\Contact\Entity' => 'ContactEntities',
                    ],
                ],
                'ContactEntities' => [
                    'class' => AttributeDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ],
            ],
        ];
    }
}
