<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 3/10/2017
 * Time: 6:37 PM
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
