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

use TYPO3\CMS\Backend\Configuration\TypoScript\ConditionMatching\ConditionMatcher;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Configuration\Parser\PageTsConfigParser;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use WebentwicklerAt\OpenidConnect\Controller\TypoScriptFrontendController;
use WebentwicklerAt\OpenidConnect\Service\AuthenticationService;

abstract class AbstractAuthenticationServiceHook
{
    const DEFAULT_MAPPING_CONFIGURATION = 'EXT:openid_connect/Configuration/TypoScript/Mapping.typoscript';

    const MODE_FE = 'FE';
    const MODE_BE = 'BE';

    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var ContentObjectRenderer
     */
    protected $cObj;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openid_connect'] ?? [];
        $this->loadTypoScriptSettings();
        $this->cObj = GeneralUtility::makeInstance(
            ContentObjectRenderer::class,
            GeneralUtility::makeInstance(TypoScriptFrontendController::class)
        );
    }

    /**
     * @param array $params
     * @param AuthenticationService $authenticationService
     * @return array
     */
    abstract public function getUser(
        array $params,
        AuthenticationService $authenticationService
    ): array;

    /**
     * @param array $params
     * @param AuthenticationService $authenticationService
     * @return array
     */
    abstract public function authUser(
        array $params,
        AuthenticationService $authenticationService
    ): array;

    /**
     * @return void
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    protected function loadTypoScriptSettings()
    {
        $filename = $this->extensionConfiguration['mappingConfiguration'] ?? static::DEFAULT_MAPPING_CONFIGURATION;
        $url = GeneralUtility::getFileAbsFileName($filename);
        $content = GeneralUtility::getUrl($url);
        $parser = GeneralUtility::makeInstance(
            PageTsConfigParser::class,
            GeneralUtility::makeInstance(TypoScriptParser::class),
            GeneralUtility::makeInstance(CacheManager::class)->getCache('hash')
        );
        $matcher = GeneralUtility::makeInstance(ConditionMatcher::class);
        $typoScript = $parser->parse($content, $matcher);
        $mode = (TYPO3_MODE === 'FE') ? static::MODE_FE : static::MODE_BE;
        $this->settings = $typoScript['config.']['tx_openidconnect.']['settings.'][$mode . '.'];
    }

    /**
     * @param array $object
     * @param array $settings
     * @param array $data
     * @return void
     */
    protected function mapFields(array &$object, array $settings, array $data)
    {
        foreach ($settings as $fieldName => $name) {
            if (substr($fieldName, -1) !== '.') {
                $conf = $settings[$fieldName . '.'];
                $object[$fieldName] = $this->mapField($name, $conf, $data);
            }
        }
    }

    /**
     * @param string $name
     * @param array $conf
     * @param array $data
     * @return string
     */
    protected function mapField(string $name, array $conf, array $data): string
    {
        $this->cObj->start($data);
        return $this->cObj->cObjGetSingle($name, $conf);
    }
}
