<?php
declare(strict_types=1);

defined('TYPO3_MODE') or die();

(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'WebentwicklerAt.OpenidConnect',
        'RedirectToLogin',
        'LLL:EXT:openid_connect/Resources/Private/Language/Backend.xlf:plugin.RedirectToLogin.title'
    );
})();
