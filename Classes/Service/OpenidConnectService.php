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

use Jumbojett\OpenIDConnectClient;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WebentwicklerAt\OpenidConnect\LoginProvider\OpenidConnectLoginProvider;

class OpenidConnectService implements SingletonInterface
{
    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function setSettings(?array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getSettings(): ?array
    {
        return $this->settings;
    }

    /**
     * @var OpenidConnectLoginProvider
     */
    protected $client;

    /**
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openid_connect'] ?: [];
        $this->settings = $settings;
        $this->client = GeneralUtility::makeInstance(
            OpenIDConnectClient::class,
            $this->extensionConfiguration['discoveryUrl'] ?: null,
            $this->extensionConfiguration['clientId'] ?: null,
            $this->extensionConfiguration['clientSecret'] ?: null
        );
    }

    public function discover()
    {

    }

    /**
     * @param Settings|null $settings
     * @return bool
     * @throws \Exception
     */
    public function auth(?Settings $settings = null): bool
    {
        $this->setClientSettings($settings);
        $isAuthenticated = $this->client->authenticate();
        if ($isAuthenticated) {
            $tokenResponse = $this->client->getTokenResponse();
            //if (!isset($_SESSION)) {
            //    @session_start();
            //}
            //$_SESSION['tx_openidconnect'] = json_encode($tokenResponse);
        }
        return $isAuthenticated;
    }

    public function token()
    {

    }

    /**
     * @param Settings|null $settings
     * @return void
     * @throws \Exception
     */
    public function logout(?Settings $settings = null): void
    {
        $idToken = ''; // todo
        $redirect = $settings->getRedirectUri();
        $this->client->signOut($idToken, $redirect);
    }

    /**
     * @param string|null $attribute
     * @return mixed|null
     * @throws \Exception
     */
    public function userinfo(?string $attribute = null)
    {
        return $this->client->requestUserInfo($attribute);
    }

    public function revoke()
    {
        $token = ''; // todo
        $this->client->revokeToken($token);
    }

    /**
     * @param Settings|null $settings
     * @return void
     */
    protected function setClientSettings(?Settings $settings): void
    {
        if ($settings) {
            if ($settings->getRedirectUri()) {
                $this->client->setRedirectURL($settings->getRedirectUri());
            }
        }
    }
}
