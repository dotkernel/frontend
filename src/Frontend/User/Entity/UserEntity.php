<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/23/2016
 * Time: 1:29 AM
 */

namespace Dot\Frontend\User\Entity;

/**
 * Class UserEntity
 * @package Dot\Frontend\User\Entity
 */
class UserEntity extends \Dot\User\Entity\UserEntity
{
    /** @var  UserDetailsEntity */
    protected $details;

    /**
     * @return UserDetailsEntity
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param UserDetailsEntity $details
     * @return UserEntity
     */
    public function setDetails(UserDetailsEntity $details = null)
    {
        $this->details = $details;
        return $this;
    }


}