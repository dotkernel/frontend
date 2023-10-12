<?php

declare(strict_types=1);

namespace FrontendTest\Unit\App\Common;

use Frontend\App\Common\UuidAwareInterface;
use Frontend\App\Common\UuidAwareTrait;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class UuidAwareTest extends TestCase
{
    public function testAll(): void
    {
        $entity = new class implements UuidAwareInterface {
            use UuidAwareTrait;
        };

        $uuid = $entity->getUuid();
        $this->assertInstanceOf(UuidInterface::class, $uuid);
        $this->assertSame($uuid, $entity->getUuid());
    }
}