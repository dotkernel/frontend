<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/23/2016
 * Time: 1:37 AM
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