<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use DateTimeImmutable;

/**
 * Class AbstractEntity
 * @package Frontend\App\Common
 */
abstract class AbstractEntity implements UuidAwareInterface, TimestampAwareInterface
{
    use UuidAwareTrait;
    use TimestampAwareTrait;

    /**
     * AbstractEntity constructor.
     */
    public function __construct()
    {
        $this->uuid = UuidOrderedTimeGenerator::generateUuid();
        $this->created = new DateTimeImmutable();
        $this->updated = new DateTimeImmutable();
    }

    /**
     * Exchange internal values from provided array
     *
     */
    public function exchangeArray(array $data): void
    {
        foreach ($data as $property => $values) {
            if (is_array($values)) {
                $method = 'add' . ucfirst($property);
                if (!method_exists($this, $method)) {
                    continue;
                }

                foreach ($values as $value) {
                    $this->$method($value);
                }
            } else {
                $method = 'set' . ucfirst($property);
                if (!method_exists($this, $method)) {
                    continue;
                }

                $this->$method($values);
            }
        }
    }
}
