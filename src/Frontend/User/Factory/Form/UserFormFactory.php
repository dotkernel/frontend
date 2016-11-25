<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Factory\Form;

use Dot\Frontend\User\Form\InputFilter\UserDetailsInputFilter;
use Dot\Frontend\User\Form\UserDetailsFieldset;
use Dot\Frontend\User\Form\UserForm;
use Dot\User\Form\Fieldset\UserFieldset;
use Dot\User\Form\InputFilter\UserInputFilter;
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

        $userFieldset = $container->get(UserFieldset::class);
        $userInputFilter = $container->get(UserInputFilter::class);

        $detailsFieldset = $container->get(UserDetailsFieldset::class);
        $detailsInputFilter = $container->get(UserDetailsInputFilter::class);

        $form = new UserForm($userFieldset, $userInputFilter, $detailsFieldset, $detailsInputFilter, $userOptions);
        $form->init();

        return $form;
    }
}