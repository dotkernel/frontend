<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
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