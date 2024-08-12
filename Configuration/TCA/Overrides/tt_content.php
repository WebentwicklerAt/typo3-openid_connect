<?php
declare(strict_types=1);

defined('TYPO3') or die();

(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'OpenidConnect',
        'RedirectToLogin',
        'LLL:EXT:openid_connect/Resources/Private/Language/Backend.xlf:plugin.RedirectToLogin.title'
    );
})();
