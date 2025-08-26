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
    protected array $extensionConfiguration = [];

    public function __construct()
    {
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openid_connect'] ?? [];
        $this->scopes = $this->extensionConfiguration['scopes'];
    }

    protected ?string $redirectUri = null;

    public function setRedirectUri(?string $redirectUri): void
    {
        $this->redirectUri = $redirectUri;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    protected string $scopes = '';

    public function setScopes(string $scopes): void
    {
        $this->scopes = $scopes;
    }

    public function setScopesArray(array $scopes): void
    {
        $this->scopes = implode(',', $scopes);
    }

    public function addScopes(array $scopes): void
    {
        $items = $this->getScopesArray();
        $items[] = $scopes;
        $this->setScopesArray($items);
    }

    public function removeScopes(array $scopes): void
    {
        $items = $this->getScopesArray();
        $key = array_search($scopes, $items);
        if ($key !== false) {
            unset($items[$key]);
            $this->setScopesArray($items);
        }
    }

    public function getScopesArray(): array
    {
        return GeneralUtility::trimExplode(',', $this->scopes, true);
    }

    public function getScopes(): string
    {
        return $this->scopes;
    }

    public function asArray(): array
    {
        return [
            'redirectUri' => $this->redirectUri,
            'scopes' => $this->scopes,
        ];
    }
}
