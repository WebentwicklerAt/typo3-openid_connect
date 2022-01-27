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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use function Jumbojett\base64url_decode;

class OpenidConnectUtility
{
    /**
     * @param string $loginStatus
     * @param string $loginReturn
     * @param string|null $originalRedirectUri
     * @return string
     */
    public static function getRedirectUri(
        string $loginStatus,
        string $loginReturn,
        ?string $originalRedirectUri = null
    ): string
    {
        // [scheme]://[host][:[port]]
        $redirectUri = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
        // [path]
        $requestUri = parse_url(GeneralUtility::getIndpEnv('REQUEST_URI'));
        $redirectUri .= $requestUri['path'];
        // ?[query]
        $query = [];
        if (TYPO3_MODE === 'FE') {
            $query['logintype'] = $loginStatus;
        } else {
            $query['login_status'] = $loginStatus;
        }
        $query['tx_openidconnect'] = $loginReturn;
        if (static::isTrustedRedirectUrl($originalRedirectUri)) {
            $query['tx_openidconnect_redirecturi'] = $originalRedirectUri;
        }
        $redirectUri .= '?' . http_build_query($query);
        return $redirectUri;
    }

    /**
     * @param string|null $redirectUri
     * @return bool
     */
    public static function isTrustedRedirectUrl(?string $redirectUri): bool
    {
        if (empty($redirectUri)) {
            return true;
        }
        $redirectUriParts = parse_url($redirectUri);
        return GeneralUtility::isAllowedHostHeaderValue($redirectUriParts['host']);
    }

    /**
     * @param array $query
     * @return array
     */
    public static function removeOpenidConnectQueryParameter(array $query): array
    {
        if (array_key_exists('state', $query)) {
            unset($query['state']);
        }
        if (array_key_exists('session_state', $query)) {
            unset($query['session_state']);
        }
        if (array_key_exists('code', $query)) {
            unset($query['code']);
        }
        return $query;
    }

    /**
     * @param string $jwt
     * @return array
     */
    public static function decodeJwt(string $jwt): array
    {
        $data = [];
        [$data['header'], $data['payload'], $data['signature']] = explode('.', $jwt);
        foreach ($data as $key => $value) {
            $data[$key] = json_decode(base64url_decode($value));
        }
        return $data;
    }
}
