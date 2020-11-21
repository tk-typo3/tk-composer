<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

defined('TYPO3_MODE') || die();

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'tk_composer',
    'Configuration/TsConfig/Page/account.typoscript',
    'PageTS to allow only account entries'
);

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'tk_composer',
    'Configuration/TsConfig/Page/packagegroup.typoscript',
    'PageTS to allow only package group entries'
);

TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'tk_composer',
    'Configuration/TsConfig/Page/package.typoscript',
    'PageTS to allow only package entries'
);
