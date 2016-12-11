<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\Frontend\User\Service;

/**
 * Interface UserServiceInterface
 * @package Dot\Frontend\User\Service
 */
interface UserServiceInterface extends \Dot\User\Service\UserServiceInterface
{
    /**
     * @param $password
     * @param $newEmail
     * @return mixed
     */
    public function changeEmail($password, $newEmail);

    /**
     * @return mixed
     */
    public function removeAccount();
}