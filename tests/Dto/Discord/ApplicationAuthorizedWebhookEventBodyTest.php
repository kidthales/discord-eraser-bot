<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord;

use App\Dto\Discord\ApplicationAuthorizedWebhookEventBody;
use App\Enum\Discord\WebhookEventBodyType;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ApplicationAuthorizedWebhookEventBodyTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param ApplicationAuthorizedWebhookEventBody $expected
     * @param ApplicationAuthorizedWebhookEventBody $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->type, $actual->type);
        self::assertSame($expected->timestamp, $actual->timestamp);
        ApplicationAuthorizedWebhookEventDataTest::assertDeepSame($expected->data, $actual->data);
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        $subjectTemplate = '{"type":%s,"timestamp":"test-timestamp","data":%s}';

        $data = [];

        foreach (ApplicationAuthorizedWebhookEventDataTest::provider_deserialization() as [$dataTemplate, $dataExpected]) {
            $data[] = [
                sprintf($subjectTemplate, '"' . WebhookEventBodyType::ApplicationAuthorized->value . '"', $dataTemplate),
                new ApplicationAuthorizedWebhookEventBody(
                    type: WebhookEventBodyType::ApplicationAuthorized,
                    timestamp: 'test-timestamp',
                    data: $dataExpected
                )
            ];
        }

        return $data;
    }

    /**
     * @param string $subject
     * @param ApplicationAuthorizedWebhookEventBody $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, ApplicationAuthorizedWebhookEventBody $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, ApplicationAuthorizedWebhookEventBody::class, 'json');
    }
}
