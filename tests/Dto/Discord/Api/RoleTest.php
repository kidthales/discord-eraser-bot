<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord\Api;

use App\Dto\Discord\Api\Role;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RoleTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param Role $expected
     * @param Role $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->id, $actual->id);
        self::assertSame($expected->name, $actual->name);
        self::assertSame($expected->color, $actual->color);
        self::assertSame($expected->hoist, $actual->hoist);
        self::assertSame($expected->position, $actual->position);
        self::assertSame($expected->permissions, $actual->permissions);
        self::assertSame($expected->managed, $actual->managed);
        self::assertSame($expected->mentionable, $actual->mentionable);
        self::assertSame($expected->flags, $actual->flags);
        self::assertSame($expected->icon, $actual->icon);
        self::assertSame($expected->unicode_emoji, $actual->unicode_emoji);

        if (isset($expected->tags)) {
            RoleTagsTest::assertDeepSame($expected->tags, $actual->tags);
            return;
        }

        self::assertNull($actual->tags);
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        $subjectTemplate = '{"id":"test-id","name":"test-name","color":16777215,"hoist":false,"position":3,"permissions":"test-permissions","managed":true,"mentionable":false,%s"flags":7}';

        $data = [];

        foreach (RoleTagsTest::provider_deserialization() as [$roleTagsTemplate, $roleTagsExpected]) {
            $data[] = [
                sprintf($subjectTemplate, sprintf('"tags":%s,', $roleTagsTemplate)),
                new Role(
                    id: 'test-id',
                    name: 'test-name',
                    color: 0xffffff,
                    hoist: false,
                    position: 3,
                    permissions: 'test-permissions',
                    managed: true,
                    mentionable: false,
                    flags: 7,
                    tags: $roleTagsExpected
                )
            ];
        }

        return [
            [
                sprintf($subjectTemplate, ''),
                new Role(
                    id: 'test-id',
                    name: 'test-name',
                    color: 0xffffff,
                    hoist: false,
                    position: 3,
                    permissions: 'test-permissions',
                    managed: true,
                    mentionable: false,
                    flags: 7
                )
            ],
            ...$data
        ];
    }

    /**
     * @param string $subject
     * @param Role $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, Role $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, Role::class, 'json');
    }
}
