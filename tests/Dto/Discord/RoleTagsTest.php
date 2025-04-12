<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord;

use App\Dto\Discord\RoleTags;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RoleTagsTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param RoleTags $expected
     * @param RoleTags $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->getBotId(), $actual->getBotId());
        self::assertSame($expected->getIntegrationId(), $actual->getIntegrationId());
        self::assertSame($expected->getPremiumSubscriber(), $actual->getPremiumSubscriber());
        self::assertSame($expected->getSubscriptionListingId(), $actual->getSubscriptionListingId());
        self::assertSame($expected->getAvailableForPurchase(), $actual->getAvailableForPurchase());
        self::assertSame($expected->getGuildConnections(), $actual->getGuildConnections());
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        return [
            ['{}', new RoleTags(premium_subscriber: false, available_for_purchase: false, guild_connections: false)],
            [
                '{"bot_id":"test-bot-id"}',
                new RoleTags(
                    bot_id: 'test-bot-id',
                    premium_subscriber: false,
                    available_for_purchase: false,
                    guild_connections: false
                )
            ],
            [
                '{"integration_id":"test-integration-id"}',
                new RoleTags(
                    integration_id: 'test-integration-id',
                    premium_subscriber: false,
                    available_for_purchase: false,
                    guild_connections: false
                )
            ],
            [
                '{"premium_subscriber":null}',
                new RoleTags(premium_subscriber: true, available_for_purchase: false, guild_connections: false)
            ],
            [
                '{"subscription_listing_id":"test-subscription-listing-id"}',
                new RoleTags(
                    premium_subscriber: false,
                    subscription_listing_id: 'test-subscription-listing-id',
                    available_for_purchase: false,
                    guild_connections: false
                )
            ],
            [
                '{"available_for_purchase":null}',
                new RoleTags(premium_subscriber: false, available_for_purchase: true, guild_connections: false)
            ],
            [
                '{"guild_connections":null}',
                new RoleTags(premium_subscriber: false, available_for_purchase: false, guild_connections: true)
            ],
            [
                '{"bot_id":"test-bot-id","integration_id":"test-integration-id"}',
                new RoleTags(
                    bot_id: 'test-bot-id',
                    integration_id: 'test-integration-id',
                    premium_subscriber: false,
                    available_for_purchase: false,
                    guild_connections: false
                )
            ],
            [
                '{"bot_id":"test-bot-id","subscription_listing_id":"test-subscription-listing-id"}',
                new RoleTags(
                    bot_id: 'test-bot-id',
                    premium_subscriber: false,
                    subscription_listing_id: 'test-subscription-listing-id',
                    available_for_purchase: false,
                    guild_connections: false
                )
            ],
            [
                '{"integration_id":"test-integration-id","subscription_listing_id":"test-subscription-listing-id"}',
                new RoleTags(
                    integration_id: 'test-integration-id',
                    premium_subscriber: false,
                    subscription_listing_id: 'test-subscription-listing-id',
                    available_for_purchase: false,
                    guild_connections: false
                )
            ],
            [
                '{"bot_id":"test-bot-id","integration_id":"test-integration-id","subscription_listing_id":"test-subscription-listing-id"}',
                new RoleTags(
                    bot_id: 'test-bot-id',
                    integration_id: 'test-integration-id',
                    premium_subscriber: false,
                    subscription_listing_id: 'test-subscription-listing-id',
                    available_for_purchase: false,
                    guild_connections: false
                )
            ]
        ];
    }

    /**
     * @param string $subject
     * @param RoleTags $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, RoleTags $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, RoleTags::class, 'json');
    }
}
