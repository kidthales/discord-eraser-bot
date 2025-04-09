<?php

declare(strict_types=1);

namespace App\Dto\Discord;

use App\Enum\Discord\DefaultMessageNotificationLevel;
use App\Enum\Discord\ExplicitContentFilterLevel;
use App\Enum\Discord\GuildNsfwLevel;
use App\Enum\Discord\MfaLevel;
use App\Enum\Discord\PremiumTier;
use App\Enum\Discord\VerificationLevel;

/**
 * @see https://discord.com/developers/docs/resources/guild#guild-object-guild-structure
 */
final readonly class Guild
{
    /**
     * Voice region id for the guild (deprecated).
     * @var string|null
     * @deprecated
     */
    public ?string $region;

    /**
     * @param string $id Guild id.
     * @param string|null $name Guild name (2-100 characters, excluding trailing and leading whitespace).
     * @param string|null $icon Icon hash.
     * @param string|null $splash Splash hash.
     * @param string|null $discovery_splash Discovery splash hash; only present for guilds with the "DISCOVERABLE"
     * feature.
     * @param string|null $owner_id ID of owner.
     * @param string|null $afk_channel_id ID of afk channel.
     * @param int|null $afk_timeout AFK timeout in seconds.
     * @param VerificationLevel|null $verification_level Verification level required for the guild.
     * @param DefaultMessageNotificationLevel|null $default_message_notifications Default message notifications level.
     * @param ExplicitContentFilterLevel|null $explicit_content_filter Explicit content filter level.
     * @param Role[] $roles Roles in the guild.
     * @param Emoji[] $emojis Custom guild emojis.
     * @param string[] $features Enabled guild features.
     * @param MfaLevel|null $mfa_level Required MFA level for the guild.
     * @param string|null $application_id Application id of the guild creator if it is bot-created.
     * @param string|null $system_channel_id The id of the channel where guild notices such as welcome messages and
     * boost events are posted.
     * @param int|null $system_channel_flags System channel flags.
     * @param string|null $rules_channel_id The id of the channel where Community guilds can display rules and/or
     * guidelines.
     * @param string|null $vanity_url_code The vanity url code for the guild.
     * @param string|null $description The description of the guild.
     * @param string|null $banner Banner hash.
     * @param PremiumTier|null $premium_tier Premium tier (Sever Boost level).
     * @param string|null $preferred_locale The preferred locale of a Community guild; used in server discovery and
     * notices from Discord, and sent in interactions; defaults to "en-US".
     * @param string|null $public_updates_channel_id The id of the channel where admins and moderators of Community
     * guilds receive notices from Discord.
     * @param GuildNsfwLevel|null $nsfw_level Guild NSFW level.
     * @param bool $premium_progress_bar_enabled Whether the guild has the boost progress bar enabled.
     * @param string|null $safety_alerts_channel_id The id of the channel where admins and moderators of Community
     * guilds receive safety alerts from Discord.
     * @param string|null $icon_hash Icon hash, returned when in the template object.
     * @param bool|null $owner True if the user is the owner of the guild.
     * @param string|null $permissions Total permissions for the user in the guild (excludes overwrites and implicit
     * permissions).
     * @param string|null $region Voice region id for the guild (deprecated).
     * @param bool|null $widget_enabled True if the server widget is enabled.
     * @param string|null $widget_channel_id The channel id that the widget will generate an invite to, or null if set
     * to no invite.
     * @param int|null $max_presences The maximum number of presences for the guild (null is always returned, apart from
     * the largest of guilds).
     * @param int|null $max_members The maximum number of members for the guild.
     * @param int|null $premium_subscription_count The number of boosts this guild currently has.
     * @param int|null $max_video_channel_users The maximum amount of users in a video channel.
     * @param int|null $max_stage_video_channel_users The maximum amount of users in a stage video channel.
     * @param int|null $approximate_member_count Approximate number of members in this guild, returned from the
     * GET /guilds/<id> and /users/@me/guilds endpoints when with_counts is true.
     * @param int|null $approximate_presence_count Approximate number of non-offline members in this guild, returned
     * from the GET /guilds/<id> and /users/@me/guilds endpoints when with_counts is true.
     * @param WelcomeScreen|null $welcome_screen The welcome screen of a Community guild, shown to new
     * members, returned in an invite's guild object.
     * @param Sticker[]|null $stickers Custom guild stickers.
     */
    public function __construct(
        public string                           $id,
        public ?string                          $name,
        public ?string                          $icon,
        public ?string                          $splash,
        public ?string                          $discovery_splash,
        public ?string                          $owner_id,
        public ?string                          $afk_channel_id,
        public ?int                             $afk_timeout,
        public ?VerificationLevel               $verification_level,
        public ?DefaultMessageNotificationLevel $default_message_notifications,
        public ?ExplicitContentFilterLevel      $explicit_content_filter,
        public ?array                           $roles,
        public ?array                           $emojis,
        public array                            $features,
        public ?MfaLevel                        $mfa_level,
        public ?string                          $application_id,
        public ?string                          $system_channel_id,
        public ?int                             $system_channel_flags,
        public ?string                          $rules_channel_id,
        public ?string                          $vanity_url_code,
        public ?string                          $description,
        public ?string                          $banner,
        public ?PremiumTier                     $premium_tier,
        public ?string                          $preferred_locale,
        public ?string                          $public_updates_channel_id,
        public ?GuildNsfwLevel                  $nsfw_level,
        public ?bool                            $premium_progress_bar_enabled,
        public ?string                          $safety_alerts_channel_id,
        public ?string                          $icon_hash = null,
        public ?bool                            $owner = null,
        public ?string                          $permissions = null,
        ?string                                 $region = null,
        public ?bool                            $widget_enabled = null,
        public ?string                          $widget_channel_id = null,
        public ?int                             $max_presences = null,
        public ?int                             $max_members = null,
        public ?int                             $premium_subscription_count = null,
        public ?int                             $max_video_channel_users = null,
        public ?int                             $max_stage_video_channel_users = null,
        public ?int                             $approximate_member_count = null,
        public ?int                             $approximate_presence_count = null,
        public ?WelcomeScreen                   $welcome_screen = null,
        public ?array                           $stickers = null,
    )
    {
        $this->region = $region;
    }
}
