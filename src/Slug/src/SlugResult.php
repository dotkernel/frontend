<?php

declare(strict_types=1);

namespace Frontend\Slug;

/**
 * Class SlugResult
 * @package Frontend\Slug
 */
final class SlugResult
{
    private ?Slug $slug = null;

    private string $url;

    private array $matchedParams;

    private bool $success;

    /**
     * Create an instance representing a slug success from the matching slug.
     *
     * @param $slug
     * @param $url
     */
    public static function fromSlug(?\Frontend\Slug\Slug $slug, string $url, array $matchedParams = []): self
    {
        $self = new self();
        $self->success = true;
        $self->slug = $slug;
        $self->url = $url;
        $self->matchedParams = $matchedParams;

        return $self;
    }

    /**
     * Create an instance representing a slug failure.
     *
     */
    public static function fromSlugFailure(): self
    {
        $self = new self();
        $self->success = false;

        return $self;
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
     */
    public function getMatchedSlug(): bool|Slug|null
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
