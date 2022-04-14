<?php
defined('TYPO3_MODE') or die();

(function () {
    $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openid_connect'] ?? [];

    $authenticationServiceProcessSubtypeArray = [];
    $authenticationServiceSubtypeArray = [];
    if ($extensionConfiguration['enableBackendLogin']) {
        $authenticationServiceProcessSubtypeArray[] = 'processLoginDataBE';
        $authenticationServiceSubtypeArray[] = 'getUserBE';
        $authenticationServiceSubtypeArray[] = 'authUserBE';
    }
    if ($extensionConfiguration['enableFrontendLogin']) {
        $authenticationServiceProcessSubtypeArray[] = 'processLoginDataFE';
        $authenticationServiceSubtypeArray[] = 'getUserFE';
        $authenticationServiceSubtypeArray[] = 'authUserFE';
    }
    $authenticationServiceProcessSubtype = implode(',', $authenticationServiceProcessSubtypeArray);
    $authenticationServiceSubtype = implode(',', $authenticationServiceSubtypeArray);

    if ($authenticationServiceProcessSubtype) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
            'openid_connect',
            'auth',
            'tx_openidconnect_authenticationservice_process',
            [
                'title' => 'OpenID Connect Authentication',
                'description' => 'OpenID Connect processing login information service for Frontend and Backend',
                'subtype' => $authenticationServiceProcessSubtype,
                'available' => true,
                'priority' => 35,
                // Must be lower than for \TYPO3\CMS\Sv\AuthenticationService (50) to let other processing take place before
                'quality' => 50,
                'os' => '',
                'exec' => '',
                'className' => \WebentwicklerAt\OpenidConnect\Service\AuthenticationService::class
            ]
        );
    }

    if ($authenticationServiceSubtype) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
            'openid_connect',
            'auth',
            'tx_openidconnect_authenticationservice',
            [
                'title' => 'OpenID Connect Authentication',
                'description' => 'OpenID Connect authentication service for Frontend and Backend',
                'subtype' => $authenticationServiceSubtype,
                'available' => true,
                'priority' => 75,
                // Must be higher than for \TYPO3\CMS\Sv\AuthenticationService (50) or \TYPO3\CMS\Sv\AuthenticationService will log failed login attempts
                'quality' => 50,
                'os' => '',
                'exec' => '',
                'className' => \WebentwicklerAt\OpenidConnect\Service\AuthenticationService::class
            ]
        );

        if ($extensionConfiguration['enableAuthenticationServiceHooks']) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tx_openidconnect']['AuthenticationService']['getUser']['tx_openidconnect'] =
                \WebentwicklerAt\OpenidConnect\Hook\AuthenticationServiceHook::class . '->getUser';

            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tx_openidconnect']['AuthenticationService']['authUser']['tx_openidconnect'] =
                \WebentwicklerAt\OpenidConnect\Hook\AuthenticationServiceHook::class . '->authUser';
        }
    }

    if ($extensionConfiguration['enableBackendLogin']) {
        // Use popup window to refresh login instead of the AJAX relogin:
        $GLOBALS['TYPO3_CONF_VARS']['BE']['showRefreshLoginPopup'] = 1;

        $openidConnectLoginProviderKey = \WebentwicklerAt\OpenidConnect\LoginProvider\OpenidConnectLoginProvider::LOGIN_PROVIDER_KEY;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][$openidConnectLoginProviderKey] = [
            'provider' => \WebentwicklerAt\OpenidConnect\LoginProvider\OpenidConnectLoginProvider::class,
            'sorting' => 25,
            'icon-class' => 'fa-openid',
            'label' => 'LLL:EXT:openid_connect/Resources/Private/Language/locallang.xlf:openid_connect_login_provider.login.link'
        ];

        if ($extensionConfiguration['enableBackendAutoLogin']) {
            $autoLoginProviderKey = \WebentwicklerAt\OpenidConnect\LoginProvider\AutoLoginProvider::LOGIN_PROVIDER_KEY;
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][$autoLoginProviderKey] = [
                'provider' => \WebentwicklerAt\OpenidConnect\LoginProvider\AutoLoginProvider::class,
                'sorting' => 100,
                'icon-class' => 'fa-link',
                'label' => 'LLL:EXT:openid_connect/Resources/Private/Language/locallang.xlf:auto_login_provider.login.link'
            ];
        }
    }

    if ($extensionConfiguration['enableFrontendLogin']) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'WebentwicklerAt.OpenidConnect',
            'Authentication',
            [
                'Authentication' => 'index',
            ],
            [
                'Authentication' => 'index',
            ]
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'WebentwicklerAt.OpenidConnect',
            'RedirectToLogin',
            [
                'Redirect' => 'toLogin',
            ],
            [
                'Redirect' => 'toLogin',
            ]
        );
    }
})();
