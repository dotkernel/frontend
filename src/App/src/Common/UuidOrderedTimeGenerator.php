<?php

declare(strict_types=1);

namespace Frontend\App\Common;

use Ramsey\Uuid\Codec\OrderedTimeCodec;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Class UuidOrderedTimeGenerator
 * @package Frontend\App\Common
 */
final class UuidOrderedTimeGenerator
{
    private static UuidFactoryInterface $uuidFactory;

    public static function generateUuid(): UuidInterface
    {
        return self::getFactory()->uuid1();
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     */
    private static function getFactory(): UuidFactoryInterface
    {
        self::$uuidFactory = clone Uuid::getFactory();
        $orderedTimeCodec = new OrderedTimeCodec(self::$uuidFactory->getUuidBuilder());
        self::$uuidFactory->setCodec($orderedTimeCodec);

        return self::$uuidFactory;
    }
}
