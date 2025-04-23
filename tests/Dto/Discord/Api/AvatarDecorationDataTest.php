<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord\Api;

use App\Dto\Discord\Api\AvatarDecorationData;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AvatarDecorationDataTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param AvatarDecorationData $expected
     * @param AvatarDecorationData $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->asset, $actual->asset);
        self::assertSame($expected->sku_id, $actual->sku_id);
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        return [
            [
                '{"asset":"test-asset","sku_id":"test-sku-id"}',
                new AvatarDecorationData(asset: 'test-asset', sku_id: 'test-sku-id')
            ]
        ];
    }

    /**
     * @param string $subject
     * @param AvatarDecorationData $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, AvatarDecorationData $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, AvatarDecorationData::class, 'json');
    }
}
