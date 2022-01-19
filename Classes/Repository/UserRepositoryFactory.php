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

final class UserRepositoryFactory
{
    /**
     * @return UserRepositoryInterface
     */
    public static function getInstance(): UserRepositoryInterface
    {
        if (TYPO3_MODE === 'FE') {
            $instance = GeneralUtility::makeInstance(FrontendUserRepository::class);
        } else {
            $instance = GeneralUtility::makeInstance(BackendUserRepository::class);
        }
        return $instance;
    }
}
