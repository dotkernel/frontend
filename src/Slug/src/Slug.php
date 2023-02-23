<?php

declare(strict_types=1);

namespace Frontend\Slug;

/**
 * Class Slug
 * @package Frontend\Slug
 */
final class Slug
{
    /**
     * @var string
     */
    public const REQUEST_TYPE = 'request';

    /**
     * @var string
     */
    public const URL_TYPE = 'url';

    private string $alias;

    private string $routeName;

    private array $params;

    private array $exchange;

    private string $type = self::URL_TYPE;

    public function __construct(string $alias, string $routeName, array $params = [], array $exchange = [])
    {
        $this->alias = $alias;
        $this->routeName = $routeName;
        $this->params = $params;
        $this->exchange = $exchange;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function setRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    public function getExchange(): array
    {
        return $this->exchange;
    }

    public function setExchange(array $exchange): void
    {
        $this->exchange = $exchange;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
