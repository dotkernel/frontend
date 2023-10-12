<?php

declare(strict_types=1);

namespace Frontend\User\Exception;

use RuntimeException;

class AuthenticationAdapterException extends RuntimeException
{
    public static function noCredentialsProvided(): self
    {
        return new self("No credentials provided.");
    }

    public static function invalidParam(string $param): self
    {
        return new self(sprintf(
            "Missing or invalid param '%s' provided.",
            $param
        ));
    }

    public static function methodNotExists(string $method, string $class): self
    {
        return new self(sprintf(
            "Method '%s' not found in '%s'.",
            $method,
            $class,
        ));
    }

    public static function invalidOptionValue(string $option, string $property): self
    {
        return new self(sprintf(
            "No or invalid '%s' provided for option '%s'.",
            $option,
            $property
        ));
    }

    public static function invalidConfigurationProvided(): self
    {
        return new self("No or invalid authentication configuration provided.",);
    }
}