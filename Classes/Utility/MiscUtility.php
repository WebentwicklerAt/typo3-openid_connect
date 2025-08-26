<?php

declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Utility;

/*
 * This file is part of the openid_connect extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use WebentwicklerAt\OpenidConnect\Exception\InvalidModeException;

class MiscUtility
{
    public const MODE_FE = 'FE';
    public const MODE_BE = 'BE';

    /**
     * @throws \Exception
     */
    public static function randomString(int $length = 32): string
    {
        $bytes = random_bytes($length);
        $hex = bin2hex($bytes);
        return substr($hex, 0, $length);
    }

    /**
     * @throws InvalidModeException
     */
    public static function getModeFromAuthenticationServiceSubtype(string $authenticationServiceSubtype): string
    {
        $mode = substr($authenticationServiceSubtype, -2);
        if (!static::isValidMode($mode)) {
            throw new InvalidModeException('Mode has to be "FE" or "BE", but "' . $mode . '" given.', 1723441307);
        }
        return $mode;
    }

    public static function isValidMode(string $mode): bool
    {
        return in_array($mode, [static::MODE_FE, static::MODE_BE], true);
    }
}
