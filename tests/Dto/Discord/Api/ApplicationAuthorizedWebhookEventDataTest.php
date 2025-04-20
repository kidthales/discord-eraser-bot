<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord\Api;

use App\Dto\Discord\Api\ApplicationAuthorizedWebhookEventData;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ApplicationAuthorizedWebhookEventDataTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param ApplicationAuthorizedWebhookEventData $expected
     * @param ApplicationAuthorizedWebhookEventData $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        UserTest::assertDeepSame($expected->user, $actual->user);

        self::assertSame(count($expected->scopes), count($actual->scopes));

        for ($i = 0; $i < count($expected->scopes); ++$i) {
            self::assertSame($expected->scopes[$i], $actual->scopes[$i]);
        }

        self::assertSame($expected->integration_type, $actual->integration_type);

        if (isset($expected->guild)) {
            GuildTest::assertDeepSame($expected->guild, $actual->guild);
        } else {
            self::assertNull($actual->guild);
        }
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        $subjectTemplate = '{"user":%s,"scopes":["a","bunch","of","fake","scopes"],"integration_type":0,"guild":%s}';

        $data = [];

        foreach (UserTest::provider_deserialization() as [$userTemplate, $userExpected]) {
            foreach (GuildTest::provider_deserialization() as [$guildTemplate, $guildExpected]) {
                $data[] = [
                    sprintf($subjectTemplate, $userTemplate, $guildTemplate),
                    new ApplicationAuthorizedWebhookEventData(
                        user: $userExpected,
                        scopes: ['a', 'bunch', 'of', 'fake', 'scopes'],
                        integration_type: 0,
                        guild: $guildExpected
                    )
                ];
            }
        }

        return $data;
    }

    /**
     * @param string $subject
     * @param ApplicationAuthorizedWebhookEventData $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, ApplicationAuthorizedWebhookEventData $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, ApplicationAuthorizedWebhookEventData::class, 'json');
    }
}
