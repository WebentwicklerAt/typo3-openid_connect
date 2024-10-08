<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'tx-openidconnect-openid' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:openid_connect/Resources/Public/Svg/Openid.svg',
    ],
    'tx-openidconnect-link' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:openid_connect/Resources/Public/Svg/Link.svg',
    ],
];
