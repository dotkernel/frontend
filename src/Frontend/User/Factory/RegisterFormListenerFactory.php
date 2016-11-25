<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 10/25/2016
 * Time: 8:55 PM
 */

namespace Dot\Frontend\User\Factory;

use Dot\Frontend\User\Form\InputFilter\UserDetailsInputFilter;
use Dot\Frontend\User\Form\UserDetailsFieldset;
use Dot\Frontend\User\Listener\RegisterFormListener;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class RegisterFormListenerFactory
 * @package Dot\Frontend\User\Factory
 */
class RegisterFormListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return RegisterFormListener
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $userOptions */
        $userOptions = $container->get(UserOptions::class);

        $userDetailsFieldset = $container->get(UserDetailsFieldset::class);
        $userDetailsFilter = $container->get(UserDetailsInputFilter::class);

        return new RegisterFormListener($userDetailsFieldset, $userDetailsFilter);
    }
}