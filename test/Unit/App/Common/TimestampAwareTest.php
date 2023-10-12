<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Common;

use Frontend\App\Common\TimestampAwareInterface;
use Frontend\App\Common\TimestampAwareTrait;
use PHPUnit\Framework\TestCase;
use DateTimeInterface;

class TimestampAwareTest extends TestCase
{
    public function testAll(): void
    {
        $entity = new class implements TimestampAwareInterface {
            use TimestampAwareTrait;
        };

        $this->assertNull($entity->getCreated());
        $this->assertNull($entity->getUpdated());

        $entity->updateTimestamps();

        $this->assertInstanceOf(DateTimeInterface::class, $entity->getCreated());
        $this->assertIsString($entity->getCreatedFormatted());
        $this->assertSame(date('Y-m-d H:i:s'), $entity->getCreatedFormatted());
        $this->assertSame(date('d-m-Y H:i:s'), $entity->getCreatedFormatted('d-m-Y H:i:s'));

        $this->assertInstanceOf(DateTimeInterface::class, $entity->getUpdated());
        $this->assertIsString($entity->getUpdatedFormatted());
        $this->assertSame(date('Y-m-d H:i:s'), $entity->getUpdatedFormatted());
        $this->assertSame(date('d-m-Y H:i:s'), $entity->getUpdatedFormatted('d-m-Y H:i:s'));

        $entity->setDateFormat('d-m-Y H:i:s');

        $this->assertIsString($entity->getCreatedFormatted());
        $this->assertSame(date('d-m-Y H:i:s'), $entity->getCreatedFormatted());
        $this->assertSame(date('Y-m-d H:i:s'), $entity->getCreatedFormatted('Y-m-d H:i:s'));

        $this->assertIsString($entity->getUpdatedFormatted());
        $this->assertSame(date('d-m-Y H:i:s'), $entity->getUpdatedFormatted());
        $this->assertSame(date('Y-m-d H:i:s'), $entity->getUpdatedFormatted('Y-m-d H:i:s'));
    }
}