<?php
defined('TYPO3_MODE') or die();

(function () {
    // Use popup window to refresh login instead of the AJAX relogin:
    $GLOBALS['TYPO3_CONF_VARS']['BE']['showRefreshLoginPopup'] = 1;

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
        'openid_connect',
        'auth',
        'tx_openidconnect_service',
        [
            'title' => 'OpenID Connect Authentication',
            'description' => 'OpenID Connect authentication service for Frontend and Backend',
            'subtype' => 'getUserFE,authUserFE,getUserBE,authUserBE',
            'available' => true,
            'priority' => 75,
            // Must be higher than for \TYPO3\CMS\Sv\AuthenticationService (50) or \TYPO3\CMS\Sv\AuthenticationService will log failed login attempts
            'quality' => 50,
            'os' => '',
            'exec' => '',
            'className' => \WebentwicklerAt\OpenidConnect\Service\AuthenticationService::class
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1642422526] = [
        'provider' => \WebentwicklerAt\OpenidConnect\LoginProvider\OpenidConnectLoginProvider::class,
        'sorting' => 25,
        'icon-class' => 'fa-openid',
        'label' => 'LLL:EXT:openid_connect/Resources/Private/Language/locallang.xlf:login.link'
    ];
})();
