<?php
declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Service;

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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Authentication\AbstractAuthenticationService;
use TYPO3\CMS\Core\Authentication\AbstractUserAuthentication;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebentwicklerAt\OpenidConnect\Repository\UserRepositoryFactory;
use WebentwicklerAt\OpenidConnect\Utility\OpenidConnectUtility;

/**
 * @see \TYPO3\CMS\Core\Authentication\AuthenticationService
 */
class AuthenticationService extends AbstractAuthenticationService implements LoggerAwareInterface, SingletonInterface
{
    use LoggerAwareTrait;

    const LOGINTYPE_LOGIN = 'login';
    const LOGINTYPE_LOGOUT = 'logout';

    const OIDC_LOGIN = 'login';
    const OIDC_LOGINRETURN = 'loginreturn';
    const OIDC_LOGOUT = 'logout';
    const OIDC_LOGOUTRETURN = 'logoutreturn';

    const PROCESS_PROCESSED = true;
    const PROCESS_PROCESSED_FINAL = 200;
    const PROCESS_UNPROCESSED = false;

    const AUTH_USER_AUTHENTICATED_FINAL = 200;
    const AUTH_USER_NOTAUTHENTICATED = 100;
    const AUTH_USER_AUTHENTICATED = 0;
    const AUTH_USER_NOTAUTHENTICATED_FINAL = -1;

    /**
     * @var AbstractUserAuthentication
     */
    protected $parentObject;

    /**
     * @var array|null
     */
    protected $userinfo;

    /**
     * Initialize authentication service
     *
     * @param string $mode Subtype of the service which is used to call the service.
     * @param array $loginData Submitted login form data
     * @param array $authInfo Information array. Holds submitted form data etc.
     * @param AbstractUserAuthentication $pObj Parent object
     */
    public function initAuth($mode, $loginData, $authInfo, $pObj)
    {
        parent::initAuth($mode, $loginData, $authInfo, $pObj);

        $this->parentObject = $pObj;
    }

    /**
     * Process the submitted credentials.
     * In this case hash the clear text password if it has been submitted.
     *
     * Returns one of the following status codes:
     *  true:   Successfully processed login data
     *  >= 200: Indicates that no further login data processing should take place.
     * false:   Otherwise
     *
     * @param array $loginData Credentials that are submitted and potentially modified by other services
     * @param string $passwordTransmissionStrategy Keyword of how the password has been hashed or encrypted before submission
     * @return bool
     */
    public function processLoginData(array &$loginData, $passwordTransmissionStrategy)
    {
        $isProcessed = static::PROCESS_UNPROCESSED;
        if (
            GeneralUtility::_GP('tx_openidconnect')
            && GeneralUtility::_GP('tx_openidconnect') === static::OIDC_LOGINRETURN
        ) {
            $originalRedirectUri = GeneralUtility::_GP('tx_openidconnect_redirecturi');
            $settings = GeneralUtility::makeInstance(Settings::class);
            $redirectUri = OpenidConnectUtility::getRedirectUri(
                static::LOGINTYPE_LOGIN,
                static::OIDC_LOGINRETURN,
                $originalRedirectUri
            );
            $settings->setRedirectUri($redirectUri);
            $oidcService = GeneralUtility::makeInstance(OpenidConnectService::class);
            $isAuthenticated = $oidcService->auth($settings);
            if ($isAuthenticated) {
                $this->userinfo = $oidcService->userinfo();
                $this->logger->debug(
                    sprintf(
                        'Returned from OpenID Connect login with userinfo "%s"',
                        print_r($this->userinfo, true)
                    )
                );
            } else {
                $this->logger->error(
                    'Returned from OpenID Connect with invalid login'
                );
            }
            $isProcessed = static::PROCESS_PROCESSED_FINAL;
        }
        return $isProcessed;
    }

    /**
     * Find a user (eg. look up the user record in database when a login is sent)
     *
     * @return mixed User array or FALSE
     */
    public function getUser()
    {
        $user = false;
        if (
            is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tx_openidconnect']['AuthenticationService']['getUser'])
            && is_array($this->userinfo)
            && count($this->userinfo)
        ) {
            $_params = [
                'user' => &$user,
                'userinfo' => $this->userinfo,
                'userRepository' => UserRepositoryFactory::getInstance(),
            ];
            foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tx_openidconnect']['AuthenticationService']['getUser'] as $_funcRef) {
                GeneralUtility::callUserFunction($_funcRef, $_params, $this);
            }
            if ($user) {
                $this->logger->debug(
                    sprintf(
                        'OpenID Connect successfully fetched user "%d" with userinfo "%s"',
                        print_r($user['uid'], true),
                        print_r($this->userinfo, true)
                    )
                );
            } else {
                $this->logger->error(
                    sprintf(
                        'OpenID Connect failed fetch user with userinfo "%s"',
                        print_r($this->userinfo, true)
                    )
                );
            }
        }
        return $user;
    }

    /**
     * Authenticate a user: Check submitted user credentials against stored hashed password,
     * check domain lock if configured.
     *
     * Returns one of the following status codes:
     *  >= 200: User authenticated successfully. No more checking is needed by other auth services.
     *  >= 100: User not authenticated; this service is not responsible. Other auth services will be asked.
     *  > 0:    User authenticated successfully. Other auth services will still be asked.
     *  <= 0:   Authentication failed, no more checking needed by other auth services.
     *
     * @param array $user User data
     * @return int Authentication status code, one of 0, 100, 200
     */
    public function authUser(array $user): int
    {
        $auth = static::AUTH_USER_NOTAUTHENTICATED;
        if (
            is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tx_openidconnect']['AuthenticationService']['authUser'])
            && is_array($this->userinfo)
            && count($this->userinfo)
        ) {
            $_params = [
                'auth' => &$auth,
                'user' => $user,
            ];
            foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tx_openidconnect']['AuthenticationService']['authUser'] as $_funcRef) {
                GeneralUtility::callUserFunction($_funcRef, $_params, $this);
            }
            if ($auth === static::AUTH_USER_AUTHENTICATED || $auth === static::AUTH_USER_AUTHENTICATED_FINAL) {
                $this->logger->debug(
                    sprintf(
                        'OpenID Connect login authentication succeeded with status code "%d" with userinfo "%s"',
                        $auth,
                        print_r($this->userinfo, true)
                    )
                );
            } else {
                $this->logger->error(
                    sprintf(
                        'OpenID Connect login authentication failed with status code "%d" with userinfo "%s"',
                        $auth,
                        print_r($this->userinfo, true)
                    )
                );
            }
        }
        return $auth;
    }
}
