<?php

declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Hook;

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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\TypoScript\TypoScriptStringFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\FrontendSimulatorUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use WebentwicklerAt\OpenidConnect\Service\AuthenticationService;
use WebentwicklerAt\OpenidConnect\Utility\MiscUtility;

abstract class AbstractAuthenticationServiceHook implements AuthenticationServiceHookInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const DEFAULT_MAPPING_CONFIGURATION = 'EXT:openid_connect/Configuration/TypoScript/Mapping.typoscript';

    protected array $extensionConfiguration = [];

    protected array $settings = [];

    protected ContentObjectRenderer $cObj;

    public function __construct()
    {
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openid_connect'] ?? [];
        if (empty($GLOBALS['TSFE'])) {
            FrontendSimulatorUtility::simulateFrontendEnvironment();
        }
        $this->cObj = $GLOBALS['TSFE']->cObj;
        $request = ServerRequestFactory::fromGlobals();
        $this->cObj->setRequest($request);
    }

    abstract public function getUser(
        array $params,
        AuthenticationService $authenticationService,
    ): array;

    abstract public function authUser(
        array $params,
        AuthenticationService $authenticationService,
    ): array;

    protected function loadTypoScriptSettings(AuthenticationService $authenticationService)
    {
        $filename = $this->extensionConfiguration['mappingConfiguration'] ?? static::DEFAULT_MAPPING_CONFIGURATION;
        $url = GeneralUtility::getFileAbsFileName($filename);
        $content = GeneralUtility::getUrl($url);
        /** @var TypoScriptStringFactory $typoScriptStringFactory */
        $typoScriptStringFactory = GeneralUtility::makeInstance(TypoScriptStringFactory::class);
        $cacheIdentifier = 'tx_openidconnect_' . md5($filename);
        $typoScriptTree = $typoScriptStringFactory->parseFromStringWithIncludes($cacheIdentifier, $content);
        $typoScript = $typoScriptTree->toArray();
        $mode = MiscUtility::getModeFromAuthenticationServiceSubtype($authenticationService->mode);
        $this->settings = $typoScript['config.']['tx_openidconnect.']['settings.'][$mode . '.'];
    }

    protected function mapFields(array &$object, array $settings, array $data): void
    {
        foreach ($settings as $fieldName => $name) {
            if (substr($fieldName, -1) !== '.') {
                $conf = $settings[$fieldName . '.'];
                $object[$fieldName] = $this->mapField($name, $conf, $data);
            }
        }
    }

    protected function mapField(string $name, array $conf, array $data): string
    {
        $this->cObj->start($data);
        return $this->cObj->cObjGetSingle($name, $conf);
    }
}
