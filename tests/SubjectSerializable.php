<?php

namespace App\Tests;

use LogicException;
use Symfony\Component\Serializer\SerializerInterface;

trait SubjectSerializable
{
    /**
     * @param mixed $expected
     * @param mixed $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        throw new LogicException('Override this method!');
    }

    /**
     * @return SerializerInterface
     */
    protected static function getSerializer(): SerializerInterface
    {
        return self::getContainer()->get(SerializerInterface::class);
    }

    /**
     * @param mixed $subject
     * @param string $expected
     * @param string $format
     * @return void
     */
    protected static function testSerialization(mixed $subject, string $expected, string $format): void
    {
        self::assertSame($expected, self::getSerializer()->serialize($subject, $format));
    }

    /**
     * @param string $subject
     * @param mixed $expected
     * @param string $type
     * @param string $format
     * @return void
     */
    protected static function testDeserialization(string $subject, mixed $expected, string $type, string $format): void
    {
        $actual = self::getSerializer()->deserialize($subject, $type, $format);

        self::assertInstanceOf($type, $actual);
        static::assertDeepSame($expected, $actual);
    }
}
