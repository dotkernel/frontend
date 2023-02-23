<?php

declare(strict_types=1);

namespace Frontend\Slug\Service;

use Frontend\Slug\Exception\MissingConfigurationException;
use Frontend\Slug\Slug;

/**
 * Interface SlugServiceInterface
 * @package Frontend\Slug\Service
 */
interface SlugServiceInterface
{
    /**
     * @throws MissingConfigurationException
     */
    public function slugManipulation(Slug $slug, string $attribute, string $value): mixed;
}
