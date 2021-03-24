<?php

declare(strict_types=1);

namespace Frontend\Slug;

/**
 * Class SlugResult
 * @package Frontend\Slug
 */
class SlugResult
{
    /**
     * @var ?Slug $slug
     */
    private Slug $slug;

    /** @var string $url*/
    private string $url;

    /** @var array $matchedParams */
    private array $matchedParams;

    /** @var bool $success Success state of slug. */
    private bool $success;

    /**
     *
     * Create an instance representing a slug success from the matching slug.
     *
     * @param $slug
     * @param $url
     * @param array $matchedParams
     * @return SlugResult
     */
    public static function fromSlug($slug, $url, $matchedParams = []): self
    {
        $result                  = new self();
        $result->success         = true;
        $result->slug            = $slug;
        $result->url             = $url;
        $result->matchedParams   = $matchedParams;

        return $result;
    }

    /**
     * Create an instance representing a slug failure.
     *
     * @return SlugResult
     */
    public static function fromSlugFailure(): self
    {
        $result                 = new self();
        $result->success        = false;

        return $result;
    }

    /**
     * Does the result represent successful route match?
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Retrieve the slug that resulted in the slug match.
     *
     * @return false|null|Slug
     */
    public function getMatchedSlug()
    {
        return $this->isFailure() ? false : $this->slug;
    }

    /**
     * Returns the generated Url.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Returns the matched params.
     */
    public function getMatchedParams(): array
    {
        return $this->matchedParams;
    }

    /**
     * Is this a slug match failure result?
     */
    public function isFailure(): bool
    {
        return ! $this->success;
    }
}
