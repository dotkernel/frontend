<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 12/7/2016
 * Time: 8:47 PM
 */

namespace Dot\Frontend\User\Factory\Form;


use Dot\Frontend\User\Form\InputFilter\UserDetailsInputFilter;
use Dot\Frontend\User\Form\UserDetailsFieldset;
use Dot\User\Form\UserForm;
use Interop\Container\ContainerInterface;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class UserFormDelegator
 * @package Dot\Frontend\User\Factory\Form
 */
class UserFormDelegator implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        /** @var Fieldset $userDetailsFieldset */
        $userDetailsFieldset = $container->get(UserDetailsFieldset::class);
        /** @var InputFilter $userDetailsFilter */
        $userDetailsFilter = $container->get(UserDetailsInputFilter::class);

        /** @var UserForm $form */
        $form = $callback();

        $userDetailsFieldset->setName('details');

        $form->getBaseFieldset()->add($userDetailsFieldset);

        $form->getBaseFieldset()->remove('email');
        $form->getInputFilter()->remove('email');

        $form->getInputFilter()->get('user')->add($userDetailsFilter, 'details');

        return $form;

    }
}