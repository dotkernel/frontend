<?php

declare(strict_types=1);

namespace Frontend\Slug;

use Frontend\Slug\Exception\DuplicateSlugException;

use function sprintf;

/**
 * Class DuplicateSlugDetector
 * @package Frontend\Slug
 */
final class DuplicateSlugDetector
{
    /**
     * List of all slugs indexed by alias
     *
     * @var Slug[]
     */
    private array $slugs = [];

    /**
     * Determine if the slug is duplicated in the current list.
     *
     * Checks if a slug with the same alias exists already in the list;
     *
     * @param Slug $slug
     * @throws DuplicateSlugException On duplicate slug detection.
     */
    public function detectDuplicate(Slug $slug): void
    {
        $this->throwOnDuplicate($slug);
        $this->remember($slug);
    }

    /**
     * @param Slug $slug
     */
    private function remember(Slug $slug): void
    {
        $this->slugs[$slug->getAlias()] = $slug;
    }

    /**
     * @param Slug $slug
     * @throws DuplicateSlugException
     */
    private function throwOnDuplicate(Slug $slug): void
    {
        if (isset($this->slugs[$slug->getAlias()])) {
            $this->duplicateRouteDetected($slug);
        }
    }

    /**
     * @param Slug $slug
     * @throws DuplicateSlugException
     */
    private function duplicateRouteDetected(Slug $slug): void
    {
        throw new DuplicateSlugException(
            sprintf(
                'Duplicate slug detected; alias "%s" ',
                $slug->getAlias()
            )
        );
    }
}
