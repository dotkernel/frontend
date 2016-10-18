<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
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