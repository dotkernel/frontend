<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/23/2016
 * Time: 1:29 AM
 */

namespace Dot\Frontend\User\Entity;

/**
 * Class UserDetailsEntity
 * @package Dot\Frontend\User\Entity
 */
class UserDetailsEntity
{
    /** @var  int */
    protected $userId;

    /** @var  string */
    protected $firstName;

    /** @var  string */
    protected $lastName;

    /** @var  string */
    protected $phone;

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return UserDetailsEntity
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return UserDetailsEntity
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return UserDetailsEntity
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return UserDetailsEntity
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    

}