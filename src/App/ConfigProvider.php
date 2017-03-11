<?php
/**
 * @see https://github.com/dotkernel/dot-frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Frontend\App;

use Frontend\App\Controller\ContactController;
use Frontend\App\Controller\PageController;

/**
 * Class ConfigProvider
 * @package Frontend\App
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'routes' => $this->getRoutesConfig(),
        ];
    }

    public function getRoutesConfig(): array
    {
        return [
            [
                'name' => 'home',
                'path' => '/',
                'middleware' => PageController::class,
                'allowed_methods' => ['GET'],
            ],
            [
                'name' => 'page',
                'path' => '/page[/{action}]',
                'middleware' => PageController::class,
            ],
            [
                'name' => 'contact',
                'path' => '/contact[/[{action}]]',
                'middleware' => ContactController::class,
            ],
        ];
    }
}
