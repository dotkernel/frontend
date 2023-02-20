<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Throwable;

/**
 * Trait UuidAwareTrait
 * @package Frontend\App\Common
 */
trait UuidAwareTrait
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="uuid", type="uuid_binary_ordered_time", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
    */
    protected ?UuidInterface $uuid = null;

    /**
     * @return UuidInterface|null
     */
    public function getUuid(): ?UuidInterface
    {
        if (!$this->uuid) {
            try {
                $this->uuid = UuidOrderedTimeGenerator::generateUuid();
            } catch (Throwable) {
                #TODO save the error message
            }
        }

        return $this->uuid;
    }
}
