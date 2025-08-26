<?php

declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\LoginProvider;

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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\LoginProviderInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Fluid\View\StandaloneView;
use WebentwicklerAt\OpenidConnect\Service\AuthenticationService;
use WebentwicklerAt\OpenidConnect\Utility\MiscUtility;
use WebentwicklerAt\OpenidConnect\Utility\OpenidConnectUtility;

class AutoLoginProvider extends AbstractLoginProvider implements LoginProviderInterface
{
    public const LOGIN_PROVIDER_KEY = 1433416748;

    public function render(
        StandaloneView $view,
        PageRenderer $pageRenderer,
        LoginController $loginController,
    ) {
        $request = $this->getRequest();
        $autologin = $request->getQueryParams()['autologin'] ?? null;
        $loginStatus = $request->getQueryParams()['login_status'] ?? null;
        if (
            $autologin !== '0'
            && $loginStatus === null
        ) {
            $redirectUri = OpenidConnectUtility::getRedirectUri(
                MiscUtility::MODE_BE,
                AuthenticationService::LOGINTYPE_LOGIN,
                AuthenticationService::OIDC_LOGIN,
            );
            $response = new RedirectResponse($redirectUri, 303);
            throw new PropagateResponseException($response, 1723447030);
        }
        parent::render($view, $pageRenderer, $loginController);
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
