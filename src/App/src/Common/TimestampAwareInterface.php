<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use DateTimeImmutable;

/**
 * Interface TimestampAwareInterface
 * @package Frontend\App\Common
 */
interface TimestampAwareInterface
{
    /**
     * @return DateTimeImmutable|null
     */
    public function getCreated(): ?DateTimeImmutable;

    /**
     * @return string|null
     */
    public function getCreatedFormatted(): ?string;

    /**
     * @return DateTimeImmutable|null
     */
    public function getUpdated(): ?DateTimeImmutable;

    /**
     * @return string|null
     */
    public function getUpdatedFormatted(): ?string;

    /**
     * @param string $dateFormat
     */
    public function setDateFormat(string $dateFormat): void;

    /**
     * Update internal timestamps
     */
    public function touch(): void;
}
