<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord\Api;

use App\Dto\Discord\Api\ApplicationAuthorizedWebhookEventBody;
use App\Dto\Discord\Api\WebhookEventPayload;
use App\Enum\Discord\WebhookType;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class WebhookEventPayloadTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param WebhookEventPayload $expected
     * @param WebhookEventPayload $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->application_id, $actual->application_id);
        self::assertSame($expected->type, $actual->type);

        if (isset($expected->event)) {
            switch ($expected->event::class) {
                case ApplicationAuthorizedWebhookEventBody::class:
                    ApplicationAuthorizedWebhookEventBodyTest::assertDeepSame($expected->event, $actual->event);
                    break;
                default:
                    self::fail('Unexpected webhook event body: ' . $expected->event::class);
            }
        } else {
            self::assertNull($actual->event);
        }
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        $subjectTemplate = '{"application_id":"test-application-id","type":%s,"event":%s}';

        $data = [];

        foreach (ApplicationAuthorizedWebhookEventBodyTest::provider_deserialization() as [$eventTemplate, $eventExpected]) {
            $data[] = [
                sprintf($subjectTemplate, WebhookType::Event->value, $eventTemplate),
                new WebhookEventPayload(application_id: 'test-application-id', type: WebhookType::Event, event: $eventExpected)
            ];
        }

        return $data;
    }

    /**
     * @param string $subject
     * @param WebhookEventPayload $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, WebhookEventPayload $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, WebhookEventPayload::class, 'json');
    }
}
