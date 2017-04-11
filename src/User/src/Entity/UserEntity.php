<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\Entity;

/**
 * Class UserEntity
 * @package Frontend\User\Entity
 */
class UserEntity extends \Dot\User\Entity\UserEntity
{
    /** @var  UserDetailsEntity */
    protected $details;

    /**
     * @return UserDetailsEntity
     */
    public function getDetails(): UserDetailsEntity
    {
        if (!$this->details) {
            $this->details = new UserDetailsEntity();
        }
        return $this->details;
    }

    /**
     * @param UserDetailsEntity $details
     */
    public function setDetails(UserDetailsEntity $details)
    {
        $this->details = $details;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                'details' => $this->getDetails(),
            ]
        );
    }
}
