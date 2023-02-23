<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Trait TimestampAwareTrait
 * @package Frontend\App\Common
 */
trait TimestampAwareTrait
{
    private string $dateFormat = 'Y-m-d H:i:s';

    /**
     * @ORM\Column(name="created", type="datetime_immutable")
     */
    protected DateTimeImmutable $created;

    /**
     * @ORM\Column(name="updated", type="datetime_immutable", nullable=true)
     */
    protected ?DateTimeImmutable $updated = null;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateTimestamps(): void
    {
        $this->touch();
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getCreatedFormatted(?string $dateFormat = null): string
    {
        return $this->created->format($dateFormat ?? $this->dateFormat);
    }

    public function getUpdated(): ?DateTimeImmutable
    {
        return $this->updated;
    }

    public function getUpdatedFormatted(?string $dateFormat = null): ?string
    {
        return $this->updated?->format($dateFormat ?? $this->dateFormat);
    }

    public function setDateFormat(string $dateFormat): void
    {
        $this->dateFormat = $dateFormat;
    }

    public function touch(): void
    {
        try {
            $this->updated = new DateTimeImmutable();
        } catch (Exception) {
            #TODO save the error message
        }
    }
}
