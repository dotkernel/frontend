<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use DateTimeImmutable;

use function is_array;
use function method_exists;
use function ucfirst;

abstract class AbstractEntity implements UuidAwareInterface, TimestampAwareInterface
{
    use TimestampAwareTrait;
    use UuidAwareTrait;

    public function __construct()
    {
        $this->uuid    = UuidOrderedTimeGenerator::generateUuid();
        $this->created = new DateTimeImmutable();
        $this->updated = new DateTimeImmutable();
    }

    public function exchangeArray(array $data): void
    {
        foreach ($data as $property => $values) {
            if (is_array($values)) {
                $method = 'add' . ucfirst($property);
                if (! method_exists($this, $method)) {
                    continue;
                }
                foreach ($values as $value) {
                    $this->$method($value);
                }
            } else {
                $method = 'set' . ucfirst($property);
                if (! method_exists($this, $method)) {
                    continue;
                }
                $this->$method($values);
            }
        }
    }
}
