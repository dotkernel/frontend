<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 8/6/2016
 * Time: 12:06 AM
 */

namespace Dot\Frontend\User\Service;

use Dot\User\Entity\UserEntityInterface;

/**
 * Interface UserServiceInterface
 * @package Dot\Frontend\User\Service
 */
interface UserServiceInterface extends \Dot\User\Service\UserServiceInterface
{
    /**
     * @param UserEntityInterface $user
     * @return mixed
     */
    public function updateAccountInfo(UserEntityInterface $user);
}