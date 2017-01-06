<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Listener\UserEventsListener;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class UserEventsListenerFactory
 * @package Dot\Frontend\User\Factory
 */
class UserEventsListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return UserEventsListener
     */
    public function __invoke(ContainerInterface $container)
    {
        return new UserEventsListener(
            $container->get('dot-mail.mail-service.default'),
            $container->get(ServerUrlHelper::class),
            $container->get(UrlHelper::class),
            $container->get(UserOptions::class)
        );
    }
}
