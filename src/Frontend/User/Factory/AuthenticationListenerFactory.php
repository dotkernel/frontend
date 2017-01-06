<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Listener\AuthenticationListener;
use Interop\Container\ContainerInterface;

/**
 * Class AuthenticationListenerFactory
 * @package Dot\Frontend\Factory
 */
class AuthenticationListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return AuthenticationListener
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationListener();
    }
}
