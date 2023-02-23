<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Frontend\App\Common\AbstractEntity;

/**
 * Class UserDetail
 * @ORM\Entity(repositoryClass="Frontend\User\Repository\UserDetailRepository")
 * @ORM\Table(name="user_detail")
 * @ORM\HasLifecycleCallbacks()
 * @package Frontend\User\Entity
 */
class UserDetail extends AbstractEntity
{
    /**
     * @ORM\OneToOne(targetEntity="Frontend\User\Entity\User", inversedBy="detail")
     * @ORM\JoinColumn(name="userUuid", referencedColumnName="uuid", nullable=false)
     */
    protected UserInterface $user;

    /**
     * @ORM\Column(name="firstName", type="string", length=191, nullable=true)
     */
    protected string $firstName;

    /**
     * @ORM\Column(name="lastName", type="string", length=191, nullable=true)
     */
    protected string $lastName;

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param $firstName
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param $lastName
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return array{uuid: string, firstName: string|null, lastName: string|null, created: \DateTimeImmutable, updated: \DateTimeImmutable|null}
     */
    public function getArrayCopy(): array
    {
        return [
            'uuid' => $this->getUuid()->toString(),
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated()
        ];
    }
}
