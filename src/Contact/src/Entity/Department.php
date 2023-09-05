<?php

namespace Frontend\Contact\Entity;

use Frontend\App\Common\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="Frontend\Contact\Repository\DepartmentRepository")
 * @ORM\Table(name="department")
 * @ORM\HasLifecycleCallbacks
 */
class Department extends AbstractEntity
{
    /**
     * @ORM\Column(name="name", type="string", length=191)
     */
    protected string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }


}
