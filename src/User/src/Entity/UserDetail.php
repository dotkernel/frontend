<?php

declare(strict_types=1);

namespace Frontend\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Frontend\App\Common\AbstractEntity;
use Frontend\User\Repository\UserDetailRepository;

#[ORM\Entity(repositoryClass: UserDetailRepository::class)]
#[ORM\Table(name: 'user_detail')]
#[ORM\HasLifecycleCallbacks]
class UserDetail extends AbstractEntity
{
    #[ORM\OneToOne(inversedBy: 'detail', targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'userUuid', referencedColumnName: 'uuid', nullable: false)]
    protected UserInterface $user;

    #[ORM\Column(name: 'firstName', type: 'string', length: 191, nullable: true)]
    protected string $firstName;

    #[ORM\Column(name: 'lastName', type: 'string', length: 191, nullable: true)]
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

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getArrayCopy(): array
    {
        return [
            'uuid'      => $this->getUuid()->toString(),
            'firstName' => $this->getFirstName(),
            'lastName'  => $this->getLastName(),
            'created'   => $this->getCreated(),
            'updated'   => $this->getUpdated(),
        ];
    }
}
