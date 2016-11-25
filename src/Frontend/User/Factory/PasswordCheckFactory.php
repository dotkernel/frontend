<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-admin
 * @author: n3vrax
 * Date: 11/23/2016
 * Time: 10:35 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Service\PasswordCheck;
use Interop\Container\ContainerInterface;
use Zend\Crypt\Password\PasswordInterface;

/**
 * Class PasswordCheckFactory
 * @package Dot\Admin\Factory
 */
class PasswordCheckFactory
{
    /**
     * @param ContainerInterface $container
     * @return PasswordCheck
     */
    public function __invoke(ContainerInterface $container)
    {
        return new PasswordCheck($container->get(PasswordInterface::class));
    }
}