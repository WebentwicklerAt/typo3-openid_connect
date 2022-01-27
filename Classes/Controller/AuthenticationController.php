<?php
declare(strict_types=1);

namespace WebentwicklerAt\OpenidConnect\Controller;

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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class AuthenticationController extends ActionController
{
    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * @return void
     */
    public function initializeAction()
    {
        parent::initializeAction();
        $this->extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['openid_connect'] ?? [];
    }

    /**
     * @return void
     */
    public function indexAction()
    {
    }
}
