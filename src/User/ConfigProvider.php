<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 1:30 AM
 */

declare(strict_types = 1);

namespace App\User;

use App\User\Fieldset\UserDetailsFieldset;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider
 * @package App\User
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),

            'dot_form' => $this->getFormsConfig(),

            'dot_ems' => $this->getMappersConfig(),

            'dot_user' => [

            ]
        ];
    }

    public function getDependenciesConfig(): array
    {
        return [

        ];
    }

    public function getFormsConfig(): array
    {
        return [
            'form_manager' => [
                'factories' => [
                    UserDetailsFieldset::class => InvokableFactory::class,
                ],
                'aliases' => [
                    'UserDetailsFieldset' => UserDetailsFieldset::class
                ]
            ]
        ];
    }

    public function getMappersConfig(): array
    {
        return [

        ];
    }
}
