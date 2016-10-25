<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Factory\Form;

use Dot\Frontend\User\Entity\UserEntityHydrator;
use Dot\Frontend\User\Form\InputFilter\UserDetailsInputFilter;
use Dot\Frontend\User\Form\InputFilter\UserInputFilter;
use Dot\Frontend\User\Form\UserForm;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class UserFormFactory
 * @package Dot\Frontend\User\Factory\Form
 */
class UserFormFactory
{
    /**
     * @param ContainerInterface $container
     * @return UserForm
     */
    public function __invoke(ContainerInterface $container)
    {
        $userOptions = $container->get(UserOptions::class);

        $form = new UserForm($userOptions);
        $form->init();

        $userDetailsInputFilter = new UserDetailsInputFilter($userOptions);
        $userDetailsInputFilter->init();

        $userFilter = new UserInputFilter($userOptions, $userDetailsInputFilter);
        $userFilter->init();

        $form->setInputFilter($userFilter);
        $form->setHydrator(new UserEntityHydrator());

        return $form;
    }
}