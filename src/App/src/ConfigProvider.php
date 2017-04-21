<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\App;

use Frontend\App\Form\ContactForm;
use Frontend\App\Form\UserMessageFieldset;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider
 * @package Frontend\App
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),

            'templates' => $this->getTemplates(),

            'dot_form' => $this->getForms(),
        ];
    }

    public function getDependencies(): array
    {
        return [];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => [__DIR__ . '/../templates/app'],
                'error' => [__DIR__ . '/../templates/error'],
                'layout' => [__DIR__ . '/../templates/layout'],
                'page' => [__DIR__ . '/../templates/page'],
                'partial' => [__DIR__ . '/../templates/partial'],
            ],
        ];
    }

    public function getForms()
    {
        return [
            'form_manager' => [
                'factories' => [
                    UserMessageFieldset::class => InvokableFactory::class,
                    ContactForm::class => InvokableFactory::class,
                ],
                'aliases' => [
                    'UserMessageFieldset' => UserMessageFieldset::class,
                    'ContactForm' => ContactForm::class,
                ]
            ],
        ];
    }
}
