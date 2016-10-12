<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/24/2016
 * Time: 7:46 PM
 */

namespace Dot\Frontend\User\Entity;

/**
 * Class UserEntityHydrator
 * @package Dot\Frontend\User\Entity
 */
class UserEntityHydrator extends \Dot\User\Entity\UserEntityHydrator
{
    /**
     * UserEntityHydrator constructor.
     * @param bool $underscoreSeparatedKeys
     */
    public function  __construct($underscoreSeparatedKeys = false)
    {
        parent::__construct($underscoreSeparatedKeys);
        $this->addStrategy('details', new UserDetailsHydratorStrategy(new UserDetailsHydrator()));
    }
}