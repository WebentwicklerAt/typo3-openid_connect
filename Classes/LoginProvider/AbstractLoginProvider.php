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
use TYPO3\CMS\Fluid\View\StandaloneView;
use WebentwicklerAt\OpenidConnect\Service\AuthenticationService;

abstract class AbstractLoginProvider implements LoginProviderInterface
{
    const DEFAULT_TEMPLATE = 'EXT:openid_connect/Resources/Private/Templates/Backend/Login.html';

    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openid_connect'] ?: [];
    }

    /**
     * @param StandaloneView $view
     * @param PageRenderer $pageRenderer
     * @param LoginController $loginController
     */
    public function render(
        StandaloneView $view,
        PageRenderer $pageRenderer,
        LoginController $loginController
    )
    {
        $filename = $this->extensionConfiguration['backendLoginProviderTemplate'] ?: static::DEFAULT_TEMPLATE;
        $templatePathAndFilename = GeneralUtility::getFileAbsFileName($filename);
        $view->setTemplatePathAndFilename($templatePathAndFilename);
        $view->assignMultiple([
            'tx_openidconnect' => AuthenticationService::OIDC_LOGIN,
        ]);
    }
}
