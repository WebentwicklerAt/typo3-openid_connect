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

class AuthenticationServiceHook extends AbstractAuthenticationServiceHook
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
        $username = $this->mapField(
            $this->settings['username'],
            $this->settings['username.'],
            $userinfo
        );
        $user = $userRepository->getUser($username);
        $this->logger->debug(
            sprintf(
                'OpenID Connect fetched user "%s" with userinfo "%s"',
                print_r($user, true),
                print_r($userinfo, true)
            )
        );
        if (
            $user
            && array_key_exists('update',$this->settings)
            && $this->settings['update']
        ) {
            $this->mapFields($user, $this->settings['update.'], $userinfo);
            $userRepository->updateUser($user);
            $this->logger->debug(
                sprintf(
                    'OpenID Connect updated user "%s"',
                    print_r($user, true)
                )
            );
            $user = $userRepository->getUser($username);
            $params['user'] = $user;
        } elseif (
            array_key_exists('create',$this->settings)
            && $this->settings['create']
        ) {
            $user = [];
            $this->mapFields($user, $this->settings['create.'], $userinfo);
            $userRepository->createUser($user);
            $this->logger->debug(
                sprintf(
                    'OpenID Connect created user "%s"',
                    print_r($user, true)
                )
            );
            $user = $userRepository->getUser($username);
            $params['user'] = $user;
        }
        $this->logger->debug(
            sprintf(
                'OpenID Connect returns user "%s"',
                print_r($params['user'], true),
            )
        );
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
        $this->logger->debug(
            sprintf(
                'OpenID Connect returns auth "%d"',
                $params['auth']
            )
        );
        return $params;
    }
}
