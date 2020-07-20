<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use DateTime;
use Exception;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEntity
 * @package Core\Common
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
        $this->created = new DateTime('now');
        $this->updated = new DateTime('now');
    }

    /**
     * @return UuidInterface
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateTimestamps()
    {
        $this->touch();
    }

    /**
     * Exchange internal values from provided array
     *
     * @param array $data
     * @return void
     */
    public function exchangeArray(array $data)
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

    /**
     * @return void
     */
    public function touch(): void
    {
        try {
            if (!($this->created instanceof DateTime)) {
                $this->created = new DateTime('now');
            }

            $this->updated = new DateTime('now');
        } catch (Exception $exception) {
            #TODO save the error message
        }
    }
}
