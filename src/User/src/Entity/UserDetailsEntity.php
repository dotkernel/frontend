<?php
/**
 * @see https://github.com/dotkernel/frontend/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/frontend/blob/master/LICENSE.md MIT License
 */

declare(strict_types=1);

namespace Frontend\User\Entity;

use Dot\Mapper\Entity\Entity;

/**
 * Class UserDetailsEntity
 * @package Frontend\User\Entity
 */
class UserDetailsEntity extends Entity implements \JsonSerializable
{
    /** @var  string */
    protected $userId;

    /** @var  string */
    protected $firstName;

    /** @var  string */
    protected $lastName;

    /** @var  string */
    protected $phone;

    /** @var  string */
    protected $address;

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
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
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
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
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
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
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'userId' => $this->getUserId(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'phone' => $this->getPhone(),
            'address' => $this->getAddress()
        ];
    }
}
