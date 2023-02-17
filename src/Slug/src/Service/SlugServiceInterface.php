<?php

declare(strict_types=1);

namespace Frontend\Slug\Service;

use Doctrine\DBAL\Driver\Exception;
use Frontend\Slug\Slug;

/**
 * Interface SlugServiceInterface
 * @package Frontend\Slug\Service
 */
interface SlugServiceInterface
{
    /**
     * @param string $attribute
     * @param string $value
     * @param Slug $slug
     * @return bool|string
     * @throws Exception
     */
    public function slugManipulation(Slug $slug, string $attribute, string $value): bool|string;
}
