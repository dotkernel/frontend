<?php

namespace Frontend\Slug\Service;

use Doctrine\DBAL\Driver\Exception;
use Frontend\Slug\Slug;

/**
 * Class SlugServiceInterface
 * @package Frontend\App\Service
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
    public function slugManipulation(Slug $slug, string $attribute, string $value);
}
