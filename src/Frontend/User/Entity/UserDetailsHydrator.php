<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Entity;

use Zend\Hydrator\ClassMethods;

/**
 * Class UserDetailsHydrator
 * @package Dot\Frontend\User\Entity
 */
class UserDetailsHydrator extends ClassMethods
{
    public function __construct($underscoreSeparatedKeys = false)
    {
        parent::__construct($underscoreSeparatedKeys);
    }
}