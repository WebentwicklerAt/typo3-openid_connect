<?php
declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\UserFunc;

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

class Misc
{
    /**
     * @param string $content
     * @param array $conf
     * @return string
     */
    public function randomString(string $content, array $conf): string
    {
        $length = !empty($conf['length']) ? (int)$conf['length'] : 32;
        $bytes = random_bytes($length);
        $hex = bin2hex($bytes);
        return substr($hex, 0, $length);
    }
}
