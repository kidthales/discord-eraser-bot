<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord;

use App\Dto\Discord\Emoji;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class EmojiTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param Emoji $expected
     * @param Emoji $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->id, $actual->id);
        self::assertSame($expected->name, $actual->name);
        self::assertSame($expected->roles, $actual->roles);

        if (isset($expected->user)) {
            UserTest::assertDeepSame($expected->user, $actual->user);
        } else {
            self::assertNull($actual->user);
        }

        self::assertSame($expected->require_colons, $actual->require_colons);
        self::assertSame($expected->managed, $actual->managed);
        self::assertSame($expected->animated, $actual->animated);
        self::assertSame($expected->available, $actual->available);
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        $subjectTemplate = '{"id":"test-id","name":"test-name"%s}';

        $data = [];

        foreach (UserTest::provider_deserialization() as [$userTemplate, $userExpected]) {
            $data[] = [
                sprintf($subjectTemplate, ',"roles":["test-role-1","test-role-2"],"user":' . $userTemplate),
                new Emoji(id: 'test-id', name: 'test-name', roles: ['test-role-1', 'test-role-2'], user: $userExpected)
            ];
        }

        return [
            [
                sprintf($subjectTemplate, ''),
                new Emoji(id: 'test-id', name: 'test-name')
            ],
            ...$data
        ];
    }

    /**
     * @param string $subject
     * @param Emoji $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, Emoji $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, Emoji::class, 'json');
    }
}
