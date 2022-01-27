<?php
declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Middleware;

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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use WebentwicklerAt\OpenidConnect\Service\AuthenticationService;
use WebentwicklerAt\OpenidConnect\Service\OpenidConnectService;
use WebentwicklerAt\OpenidConnect\Service\Settings;
use WebentwicklerAt\OpenidConnect\Utility\OpenidConnectUtility;

abstract class AbstractRedirect implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if (!empty($queryParams['tx_openidconnect'])) {
            if ($queryParams['tx_openidconnect'] === AuthenticationService::OIDC_LOGIN) {
                $originalRedirectUri = null;
                if (!empty($queryParams['tx_openidconnect_redirecturi'])) {
                    $originalRedirectUri = $queryParams['tx_openidconnect_redirecturi'];
                }
                $settings = GeneralUtility::makeInstance(Settings::class);
                $redirectUri = OpenidConnectUtility::getRedirectUri(
                    AuthenticationService::LOGINTYPE_LOGIN,
                    AuthenticationService::OIDC_LOGINRETURN,
                    $originalRedirectUri
                );
                $settings->setRedirectUri($redirectUri);
                $oidcService = GeneralUtility::makeInstance(OpenidConnectService::class);
                $oidcService->auth($settings);
            } elseif (
                $queryParams['tx_openidconnect'] === AuthenticationService::OIDC_LOGINRETURN
                && !empty($queryParams['tx_openidconnect_redirecturi'])
            ) {
                $originalRedirectUri = $queryParams['tx_openidconnect_redirecturi'];
                if (OpenidConnectUtility::isTrustedRedirectUrl($originalRedirectUri)) {
                    HttpUtility::redirect($originalRedirectUri);
                }
            }
        }
        return $handler->handle($request);
    }
}
