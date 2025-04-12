<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord;

use App\Dto\Discord\Sticker;
use App\Enum\Discord\StickerFormatType;
use App\Enum\Discord\StickerType;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class StickerTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param Sticker $expected
     * @param Sticker $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->id, $actual->id);
        self::assertSame($expected->name, $actual->name);
        self::assertSame($expected->description, $actual->description);
        self::assertSame($expected->tags, $actual->tags);
        self::assertSame($expected->type, $actual->type);
        self::assertSame($expected->format_type, $actual->format_type);
        self::assertSame($expected->pack_id, $actual->pack_id);
        self::assertSame($expected->available, $actual->available);
        self::assertSame($expected->guild_id, $actual->guild_id);

        if (isset($expected->user)) {
            UserTest::assertDeepSame($expected->user, $actual->user);
        } else {
            self::assertNull($actual->user);
        }

        self::assertSame($expected->sort_value, $actual->sort_value);
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        $subjectTemplate = '{"id":"test-id","name":"test-name","description":%s,"tags":"test-tag-1,test-tag-2","type":%s,"format_type":%s%s}';

        $data = [];

        foreach (UserTest::provider_deserialization() as [$userTemplate, $userExpected]) {
            foreach (StickerType::cases() as $stickerType) {
                foreach (StickerFormatType::cases() as $formatType) {
                    $data[] = [
                        sprintf($subjectTemplate, '"test-description"', $stickerType->value, $formatType->value, ',"user":' . $userTemplate),
                        new Sticker(
                            id: 'test-id',
                            name: 'test-name',
                            description: 'test-description',
                            tags: 'test-tag-1,test-tag-2',
                            type: $stickerType,
                            format_type: $formatType,
                            user: $userExpected
                        )
                    ];
                }
            }
        }

        return [
            [
                sprintf($subjectTemplate, 'null', StickerType::STANDARD->value, StickerFormatType::PNG->value, ''),
                new Sticker(
                    id: 'test-id',
                    name: 'test-name',
                    description: null,
                    tags: 'test-tag-1,test-tag-2',
                    type: StickerType::STANDARD,
                    format_type: StickerFormatType::PNG
                )
            ],
            ...$data
        ];
    }

    /**
     * @param string $subject
     * @param Sticker $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, Sticker $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, Sticker::class, 'json');
    }
}
