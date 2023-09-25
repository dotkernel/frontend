<?php

declare(strict_types=1);

namespace Frontend\Contact;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Dot\AnnotatedServices\Factory\AnnotatedServiceFactory;
use Frontend\Contact\Controller\ContactController;
use Frontend\Contact\Delegator\ContactFormDelegator;
use Frontend\Contact\Form\ContactForm;
use Frontend\Contact\Service\MessageService;
use Frontend\Contact\Service\MessageServiceInterface;
use Laminas\Form\ElementFactory;
use Mezzio\Application;

/**
 * Class ConfigProvider
 * @package Frontend\Contact
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
            'forms' => $this->getForms(),
            'doctrine' => $this->getDoctrineConfig()
        ];
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
                ContactForm::class => [
                    ContactFormDelegator::class
                ]
            ],
            'factories' => [
                ContactController::class => AnnotatedServiceFactory::class,
                MessageService::class => AnnotatedServiceFactory::class,
                ContactForm::class => ElementFactory::class
            ],
            'aliases' => [
                MessageServiceInterface::class => MessageService::class,
            ]
        ];
    }

    /**
     * @return array
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
     * @return array
     */
    public function getForms(): array
    {
        return [
            'form_manager' => [
                'factories' => [

                ],
                'aliases' => [
                ],
                'delegators' => [

                ],
            ],
        ];
    }

    /**
     * @return array
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
