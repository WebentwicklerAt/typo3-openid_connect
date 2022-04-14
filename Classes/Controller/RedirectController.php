<?php
declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Controller;

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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use WebentwicklerAt\OpenidConnect\Service\AuthenticationService;
use WebentwicklerAt\OpenidConnect\Utility\OpenidConnectUtility;

class RedirectController extends ActionController
{
    /**
     * @return void
     */
    public function toLoginAction()
    {
        $originalRedirectUri = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        $redirectUri = OpenidConnectUtility::getRedirectUri(
            AuthenticationService::LOGINTYPE_LOGIN,
            AuthenticationService::OIDC_LOGIN,
            $originalRedirectUri
        );
        $this->redirectToUri($redirectUri);
    }
}
