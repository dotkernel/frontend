<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 11/25/2016
 * Time: 9:33 PM
 */

namespace Dot\Frontend\User\Factory\Form;

use Dot\Frontend\User\Form\InputFilter\UserDetailsInputFilter;
use Interop\Container\ContainerInterface;

/**
 * Class UserDetailsInputFilterFactory
 * @package Dot\Frontend\User\Factory\Form
 */
class UserDetailsInputFilterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $filter = new UserDetailsInputFilter();
        $filter->init();

        return $filter;
    }
}