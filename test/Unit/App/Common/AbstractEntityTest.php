<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Common;

use Frontend\App\Common\AbstractEntity;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class AbstractEntityTest extends TestCase
{
    private static AbstractEntity $entity;

    public static function setUpBeforeClass(): void
    {
        self::$entity = new class extends AbstractEntity {

            public mixed $value;

            public function setValue(mixed $value)
            {
                $this->value = $value;
            }

            public function getValue(): mixed
            {
                return $this->value;
            }

            public function addValue(mixed $value)
            {
                $this->value = $value;
            }
        };
    }

    public function testGetUuid(): void
    {
        $uuid = self::$entity->getUuid();
        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame($uuid, self::$entity->getUuid());
    }

    public function testExchangeArrayAddMethod(): void
    {
        self::$entity->exchangeArray([
            'value' => [10],
        ]);

        $this->assertSame(10, self::$entity->getValue());
    }

    public function testExchangeArraySetMethod(): void
    {
        self::$entity->exchangeArray([
            'value' => 10,
        ]);

        $this->assertSame(10, self::$entity->getValue());
    }
}
