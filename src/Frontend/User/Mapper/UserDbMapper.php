<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/2/2016
 * Time: 7:28 PM
 */

namespace Dot\Frontend\User\Mapper;

use Dot\Frontend\User\Entity\UserDetailsEntity;
use Dot\Frontend\User\Entity\UserEntity;

/**
 * Class UserDbMapper
 * @package Dot\Frontend\User\Mapper
 */
class UserDbMapper extends \Dot\User\Mapper\UserDbMapper
{
    /** @var  UserDetailsMapperInterface */
    protected $userDetailsMapper;

    /**
     * @param $field
     * @param $value
     * @return UserEntity
     */
    public function findUserBy($field, $value)
    {
        /** @var UserEntity $user */
        $user = parent::findUserBy($field, $value);
        if($user) {
            $details = $this->userDetailsMapper->getUserDetails($user->getId());
            if($details) {
                $user->setDetails($details);
            }
        }

        return $user;
    }

    /**
     * @param array $filters
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function findAllUsers(array $filters = [])
    {
        $users = parent::findAllUsers($filters);
        /** @var UserEntity $user */
        foreach ($users as $user) {
            $details = $this->userDetailsMapper->getUserDetails($user->getId());
            if($details) {
                $user->setDetails($details);
            }
        }

        return $users;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function createUser($user)
    {
        $details = null;
        if($user instanceof UserEntity) {
            $details = $user->getDetails();
            //remove the details key from user insert, details are inserted separately
            $user->setDetails(null);
        }

        parent::createUser($user);
        $userId = $this->lastInsertValue;

        if(!$details) {
            $details = new UserDetailsEntity();
        }

        $details->setUserId($userId);
        return $this->userDetailsMapper->insertUserDetails($details);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function updateUser($user)
    {
        $details = null;
        if($user instanceof UserEntity) {
            $details = $user->getDetails();
            //remove the details key from user insert, details are inserted separately
            $user->setDetails(null);
        }

        parent::updateUser($user);

        if(!$details) {
            $details = new UserDetailsEntity();
            $details->setUserId($user->getId());
            //set user details back
            $user->setDetails($details);

            return $this->userDetailsMapper->insertUserDetails($details);
        }
        else {
            $details->setUserId($user->getId());
            //set user details back
            $user->setDetails($details);
            return $this->userDetailsMapper->updateUserDetails($user->getId(), $details);
        }

    }


    /**
     * @return UserDetailsMapperInterface
     */
    public function getUserDetailsMapper()
    {
        return $this->userDetailsMapper;
    }

    /**
     * @param UserDetailsMapperInterface $userDetailsMapper
     * @return UserDbMapper
     */
    public function setUserDetailsMapper($userDetailsMapper)
    {
        $this->userDetailsMapper = $userDetailsMapper;
        return $this;
    }


}