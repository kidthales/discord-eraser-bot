<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord\Api;

use App\Dto\Discord\Api\Guild;
use App\Enum\Discord\DefaultMessageNotificationLevel;
use App\Enum\Discord\ExplicitContentFilterLevel;
use App\Enum\Discord\GuildNsfwLevel;
use App\Enum\Discord\MfaLevel;
use App\Enum\Discord\PremiumTier;
use App\Enum\Discord\VerificationLevel;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class GuildTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param Guild $expected
     * @param Guild $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->id, $actual->id);
        self::assertSame($expected->name, $actual->name);
        self::assertSame($expected->icon, $actual->icon);
        self::assertSame($expected->splash, $actual->splash);
        self::assertSame($expected->discovery_splash, $actual->discovery_splash);
        self::assertSame($expected->owner_id, $actual->owner_id);
        self::assertSame($expected->afk_channel_id, $actual->afk_channel_id);
        self::assertSame($expected->afk_timeout, $actual->afk_timeout);
        self::assertSame($expected->verification_level, $actual->verification_level);
        self::assertSame($expected->default_message_notifications, $actual->default_message_notifications);
        self::assertSame($expected->explicit_content_filter, $actual->explicit_content_filter);

        if (isset($expected->roles)) {
            self::assertSame(count($expected->roles), count($actual->roles));

            for ($i = 0; $i < count($expected->roles); ++$i) {
                RoleTest::assertDeepSame($expected->roles[$i], $actual->roles[$i]);
            }
        } else {
            self::assertNull($actual->roles);
        }

        if (isset($expected->emojis)) {
            self::assertSame(count($expected->emojis), count($actual->emojis));

            for ($i = 0; $i < count($expected->emojis); ++$i) {
                EmojiTest::assertDeepSame($expected->emojis[$i], $actual->emojis[$i]);
            }
        } else {
            self::assertNull($actual->emojis);
        }

        self::assertSame(count($expected->features), count($actual->features));

        for ($i = 0; $i < count($expected->features); ++$i) {
            self::assertSame($expected->features[$i], $actual->features[$i]);
        }

        self::assertSame($expected->mfa_level, $actual->mfa_level);
        self::assertSame($expected->application_id, $actual->application_id);
        self::assertSame($expected->system_channel_id, $actual->system_channel_id);
        self::assertSame($expected->system_channel_flags, $actual->system_channel_flags);
        self::assertSame($expected->rules_channel_id, $actual->rules_channel_id);
        self::assertSame($expected->vanity_url_code, $actual->vanity_url_code);
        self::assertSame($expected->description, $actual->description);
        self::assertSame($expected->banner, $actual->banner);
        self::assertSame($expected->premium_tier, $actual->premium_tier);
        self::assertSame($expected->preferred_locale, $actual->preferred_locale);
        self::assertSame($expected->public_updates_channel_id, $actual->public_updates_channel_id);
        self::assertSame($expected->nsfw_level, $actual->nsfw_level);
        self::assertSame($expected->premium_progress_bar_enabled, $actual->premium_progress_bar_enabled);
        self::assertSame($expected->safety_alerts_channel_id, $actual->safety_alerts_channel_id);
        self::assertSame($expected->icon_hash, $actual->icon_hash);
        self::assertSame($expected->owner, $actual->owner);
        self::assertSame($expected->permissions, $actual->permissions);
        self::assertSame($expected->region, $actual->region);
        self::assertSame($expected->widget_enabled, $actual->widget_enabled);
        self::assertSame($expected->widget_channel_id, $actual->widget_channel_id);
        self::assertSame($expected->max_presences, $actual->max_presences);
        self::assertSame($expected->max_members, $actual->max_members);
        self::assertSame($expected->premium_subscription_count, $actual->premium_subscription_count);
        self::assertSame($expected->max_video_channel_users, $actual->max_video_channel_users);
        self::assertSame($expected->max_stage_video_channel_users, $actual->max_stage_video_channel_users);
        self::assertSame($expected->approximate_member_count, $actual->approximate_member_count);
        self::assertSame($expected->approximate_presence_count, $actual->approximate_presence_count);

        if (isset($expected->welcome_screen)) {
            WelcomeScreenTest::assertDeepSame($expected->welcome_screen, $actual->welcome_screen);
        } else {
            self::assertNull($actual->welcome_screen);
        }

        if (isset($expected->stickers)) {
            self::assertSame(count($expected->stickers), count($actual->stickers));

            for ($i = 0; $i < count($expected->stickers); ++$i) {
                StickerTest::assertDeepSame($expected->stickers[$i], $actual->stickers[$i]);
            }
        } else {
            self::assertNull($actual->stickers);
        }
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        $subjectTemplate = '{"id":"test-id","name":%s,"icon":%s,"splash":%s,"discovery_splash":%s,"owner_id":%s,"afk_channel_id":%s,"afk_timeout":%s,"verification_level":%s,"default_message_notifications":%s,"explicit_content_filter":%s,"roles":%s,"emojis":%s,"features":[%s],"mfa_level":%s,"application_id":%s,"system_channel_id":%s,"system_channel_flags":%s,"rules_channel_id":%s,"vanity_url_code":%s,"description":%s,"banner":%s,"premium_tier":%s,"preferred_locale":%s,"public_updates_channel_id":%s,"nsfw_level":%s,"premium_progress_bar_enabled":%s,"safety_alerts_channel_id":%s%s}';

        $roleTemplates = [];
        $rolesExpected = [];

        foreach (RoleTest::provider_deserialization() as [$roleTemplate, $roleExpected]) {
            $roleTemplates[] = $roleTemplate;
            $rolesExpected[] = $roleExpected;
        }

        $emojiTemplates = [];
        $emojisExpected = [];

        foreach (EmojiTest::provider_deserialization() as [$emojiTemplate, $emojiExpected]) {
            $emojiTemplates[] = $emojiTemplate;
            $emojisExpected[] = $emojiExpected;
        }

        $stickerTemplates = [];
        $stickersExpected = [];

        foreach (StickerTest::provider_deserialization() as [$stickerTemplate, $stickerExpected]) {
            $stickerTemplates[] = $stickerTemplate;
            $stickersExpected[] = $stickerExpected;
        }

        $data = [];

        foreach (WelcomeScreenTest::provider_deserialization() as [$welcomeScreenTemplate, $welcomeScreenExpected]) {
            $data[] = [
                sprintf(
                    $subjectTemplate,
                    '"test-name"',
                    '"test-icon"',
                    '"test-splash"',
                    '"test-discovery-splash"',
                    '"test-owner-id"',
                    '"test-afk-channel-id"',
                    10,
                    VerificationLevel::HIGH->value,
                    DefaultMessageNotificationLevel::ONLY_MENTIONS->value,
                    ExplicitContentFilterLevel::DISABLED->value,
                    '[' . implode(',', $roleTemplates) . ']',
                    '[' . implode(',', $emojiTemplates) . ']',
                    '"' . implode('","', ['a', 'bunch', 'of', 'fake', 'features']) . '"',
                    MfaLevel::ELEVATED->value,
                    '"test-application-id"',
                    '"test-system-channel-id"',
                    7,
                    '"test-rules-channel-id"',
                    '"test-vanity-url-code"',
                    '"test-description"',
                    '"test-banner"',
                    PremiumTier::TIER_2->value,
                    '"test-preferred-locale"',
                    '"public-updates-channel-id"',
                    GuildNsfwLevel::EXPLICIT->value,
                    'true',
                    '"test-safety-alerts-channel-id"',
                    ',"welcome_screen":' . $welcomeScreenTemplate . ',"stickers":[' . implode(',', $stickerTemplates) . ']'
                ),
                new Guild(
                    id: 'test-id',
                    name: 'test-name',
                    icon: 'test-icon',
                    splash: 'test-splash',
                    discovery_splash: 'test-discovery-splash',
                    owner_id: 'test-owner-id',
                    afk_channel_id: 'test-afk-channel-id',
                    afk_timeout: 10,
                    verification_level: VerificationLevel::HIGH,
                    default_message_notifications: DefaultMessageNotificationLevel::ONLY_MENTIONS,
                    explicit_content_filter: ExplicitContentFilterLevel::DISABLED,
                    roles: $rolesExpected,
                    emojis: $emojisExpected,
                    features: ['a', 'bunch', 'of', 'fake', 'features'],
                    mfa_level: MfaLevel::ELEVATED,
                    application_id: 'test-application-id',
                    system_channel_id: 'test-system-channel-id',
                    system_channel_flags: 7,
                    rules_channel_id: 'test-rules-channel-id',
                    vanity_url_code: 'test-vanity-url-code',
                    description: 'test-description',
                    banner: 'test-banner',
                    premium_tier: PremiumTier::TIER_2,
                    preferred_locale: 'test-preferred-locale',
                    public_updates_channel_id: 'public-updates-channel-id',
                    nsfw_level: GuildNsfwLevel::EXPLICIT,
                    premium_progress_bar_enabled: true,
                    safety_alerts_channel_id: 'test-safety-alerts-channel-id',
                    welcome_screen: $welcomeScreenExpected,
                    stickers: $stickersExpected
                )
            ];
        }

        return [
            [
                sprintf(
                    $subjectTemplate,
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    '',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    'null',
                    ''
                ),
                new Guild(
                    id: 'test-id',
                    name: null,
                    icon: null,
                    splash: null,
                    discovery_splash: null,
                    owner_id: null,
                    afk_channel_id: null,
                    afk_timeout: null,
                    verification_level: null,
                    default_message_notifications: null,
                    explicit_content_filter: null,
                    roles: null,
                    emojis: null,
                    features: [],
                    mfa_level: null,
                    application_id: null,
                    system_channel_id: null,
                    system_channel_flags: null,
                    rules_channel_id: null,
                    vanity_url_code: null,
                    description: null,
                    banner: null,
                    premium_tier: null,
                    preferred_locale: null,
                    public_updates_channel_id: null,
                    nsfw_level: null,
                    premium_progress_bar_enabled: null,
                    safety_alerts_channel_id: null
                )
            ],
            ...$data
        ];
    }

    /**
     * @param string $subject
     * @param Guild $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, Guild $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, Guild::class, 'json');
    }
}
