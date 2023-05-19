<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEntity
 * @package Frontend\App\Common
 */
abstract class AbstractEntity implements TimestampAwareInterface
{
    use TimestampAwareTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(name="uuid", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected ?string $uuid = null;

    /**
     * AbstractEntity constructor.
     */
    public function __construct()
    {
        $this->created = new DateTimeImmutable();
        $this->updated = new DateTimeImmutable();
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * Exchange internal values from provided array
     *
     * @param array $data
     * @return void
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
