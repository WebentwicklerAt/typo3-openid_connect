<?php

declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Repository;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebentwicklerAt\OpenidConnect\Exception\InvalidModeException;
use WebentwicklerAt\OpenidConnect\Utility\MiscUtility;

final class UserRepositoryFactory
{
    public static function getInstance(string $mode): UserRepositoryInterface
    {
        if (!MiscUtility::isValidMode($mode)) {
            throw new InvalidModeException('Mode has to be "FE" or "BE", but "' . $mode . '" given.', 1723441536);
        }
        if ($mode === MiscUtility::MODE_FE) {
            $instance = GeneralUtility::makeInstance(FrontendUserRepository::class);
        } else {
            $instance = GeneralUtility::makeInstance(BackendUserRepository::class);
        }
        return $instance;
    }
}
