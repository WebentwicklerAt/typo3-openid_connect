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

use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\LoginProviderInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use WebentwicklerAt\OpenidConnect\Service\AuthenticationService;
use WebentwicklerAt\OpenidConnect\Utility\OpenidConnectUtility;

class AutoLoginProvider implements LoginProviderInterface
{
    const LOGIN_PROVIDER_KEY = 1433416748;

    /**
     * @param StandaloneView $view
     * @param PageRenderer $pageRenderer
     * @param LoginController $loginController
     */
    public function render(
        StandaloneView $view,
        PageRenderer $pageRenderer,
        LoginController $loginController
    ) {
        if (
            GeneralUtility::_GET('autologin') !== '0'
            && GeneralUtility::_GET('login_status') === null
        ) {
            $redirectUri = OpenidConnectUtility::getRedirectUri(
                AuthenticationService::LOGINTYPE_LOGIN,
                AuthenticationService::OIDC_LOGIN
            );
            HttpUtility::redirect($redirectUri);
        }
    }
}
