<?php
declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Hook;

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

use WebentwicklerAt\OpenidConnect\Repository\UserRepositoryInterface;
use WebentwicklerAt\OpenidConnect\Service\AuthenticationService;

class AuthenticationServiceHook
{
    /**
     * @param array $params
     * @param AuthenticationService $authenticationService
     * @return array
     */
    public function getUser(
        array $params,
        AuthenticationService $authenticationService
    ): array
    {
        $userinfo = $params['userinfo'];
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $params['userRepository'];
        $user = $userRepository->getUser($userinfo->SamAccountName);
        if ($user) {
            // todo: update user
            $params['user'] = $user;
        } else {
            // todo: create user
        }
        return $params;
    }

    /**
     * @param array $params
     * @param AuthenticationService $authenticationService
     * @return array
     */
    public function authUser(
        array $params,
        AuthenticationService $authenticationService
    ): array
    {
        $user = $params['user'];
        if (is_array($user)) {
            $params['auth'] = AuthenticationService::AUTH_USER_AUTHENTICATED_FINAL;
        }
        return $params;
    }
}
