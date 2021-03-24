<?php

declare(strict_types=1);

namespace Frontend\Slug;

/**
 * Class Slug
 * @package Frontend\Slug
 */
class Slug
{
    public const  REQUEST_TYPE = 'request';

    public const URL_TYPE = 'url';

    /** @var string */
    private string $alias;

    /** @var string */
    private string $routeName;

    /** @var array */
    private array $params;

    /** @var array */
    private array $exchange;

    /** @var string */
    private string $type;

    /**
     * @param string $alias
     * @param string $routeName
     * @param array $params
     * @param array $exchange
     */
    public function __construct(
        string $alias,
        string $routeName,
        array $params = [],
        array $exchange = []
    ) {
        $this->alias        = $alias;
        $this->routeName    = $routeName;
        $this->params       = $params;
        $this->exchange     = $exchange;
        $this->type         = self::URL_TYPE;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     */
    public function setRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    /**
     * @return array
     */
    public function getExchange(): array
    {
        return $this->exchange;
    }

    /**
     * @param array $exchange
     */
    public function setExchange(array $exchange): void
    {
        $this->exchange = $exchange;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
