<?php
declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Service;

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

class Settings
{
    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openid_connect'] ?? [];
        $this->scopes = $this->extensionConfiguration['scopes'];
    }

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @param string $redirectUri
     * @return void
     */
    public function setRedirectUri(?string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    /**
     * @var string
     */
    protected $scopes;

    /**
     * @param string $scopes
     * @return void
     */
    public function setScopes(string $scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @param array $scopes
     * @return void
     */
    public function setScopesArray(array $scopes)
    {
        $this->scopes = implode(',', $scopes);
    }

    /**
     * @param
     * @return void
     */
    public function addScopes($scopes)
    {
        $items = $this->getScopesArray();
        $items[] = $scopes;
        $this->setScopesArray($items);
    }

    /**
     * @param
     * @return void
     */
    public function removeScopes($scopes)
    {
        $items = $this->getScopesArray();
        $key = array_search($scopes, $items);
        if ($key !== false) {
            unset($items[$key]);
            $this->setScopesArray($items);
        }
    }

    /**
     * @return array
     */
    public function getScopesArray()
    {
        return GeneralUtility::trimExplode(',', $this->scopes, true);
    }

    /**
     * @return string
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return [
            'redirectUri' => $this->redirectUri,
            'scopes' => $this->scopes,
        ];
    }
}
