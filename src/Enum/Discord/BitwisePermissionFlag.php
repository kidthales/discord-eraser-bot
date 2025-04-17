<?php

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/topics/permissions#permissions-bitwise-permission-flags
 */
enum BitwisePermissionFlag: string
{
    /**
     * Allows all permissions and bypasses channel permission overwrites.
     */
    case ADMINISTRATOR = '0x0000000000000008';

    /**
     * Allows management and editing of the guild.
     */
    case MANAGE_GUILD = '0x0000000000000020';

    /**
     * Allows for deletion of other users messages. (T,V,S)
     */
    case MANAGE_MESSAGES = '0x0000000000002000';

    /**
     * Allows for deleting and archiving threads, and viewing all private threads. (T)
     */
    case MANAGE_THREADS = '0x0000000400000000';

    /**
     * @param BitwisePermissionFlag $flag
     * @param string $permissions
     * @return bool
     */
    public static function isGranted(BitwisePermissionFlag $flag, string $permissions): bool
    {
        $gmpFlag = gmp_init($flag->value);
        return gmp_cmp($gmpFlag, gmp_and($permissions, $gmpFlag)) === 0;
    }
}
