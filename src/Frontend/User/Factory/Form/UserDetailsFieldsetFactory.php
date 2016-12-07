<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 11/25/2016
 * Time: 9:30 PM
 */

namespace Dot\Frontend\User\Factory\Form;

use Dot\Frontend\User\Entity\UserDetailsEntity;
use Dot\Frontend\User\Form\UserDetailsFieldset;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;

/**
 * Class UserDetailsFieldsetFactory
 * @package Dot\Frontend\User\Factory\Form
 */
class UserDetailsFieldsetFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $fieldset = new UserDetailsFieldset();

        $fieldset->setObject(new UserDetailsEntity());
        $fieldset->setHydrator(new ClassMethods(false));
        $fieldset->init();

        return $fieldset;
    }
}