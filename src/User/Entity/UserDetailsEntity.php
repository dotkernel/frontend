<?php
/**
 * @copyright: DotKernel
 * @library: dot-frontend
 * @author: n3vrax
 * Date: 2/23/2017
 * Time: 1:33 AM
 */

declare(strict_types = 1);

namespace App\User\Entity;

use Dot\Ems\Entity\Entity;

/**
 * Class UserDetailsEntity
 * @package App\User\Entity
 */
class UserDetailsEntity extends Entity
{
    /** @var  string */
    protected $firstName;

    /** @var  string */
    protected $lastName;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }
}
