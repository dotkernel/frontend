<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 7/25/2016
 * Time: 3:14 AM
 */

namespace Dot\Frontend\User\Mapper;

/**
 * Interface UserDetailsMapperInterface
 * @package Dot\Frontend\User\Mapper
 */
interface UserDetailsMapperInterface
{
    /**
     * @param $userId
     * @return mixed
     */
    public function getUserDetails($userId);

    /**
     * @param $data
     * @return mixed
     */
    public function insertUserDetails($data);

    /**
     * @param $userId
     * @param $data
     * @return mixed
     */
    public function updateUserDetails($userId, $data);
}