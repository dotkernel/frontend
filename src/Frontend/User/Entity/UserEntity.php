<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
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
        if (!$this->details) {
            $this->details = new UserDetailsEntity();
        }
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

    public function ignoredProperties()
    {
        return array_merge(parent::ignoredProperties(), ['details']);
    }
}
