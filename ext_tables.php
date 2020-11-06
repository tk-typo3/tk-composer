<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('tk_composer', 'Configuration/TypoScript', 'Composer');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_tkcomposer_domain_model_account', 'EXT:tk_composer/Resources/Private/Language/locallang_csh_tx_tkcomposer_domain_model_account.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_tkcomposer_domain_model_account');

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_tkcomposer_domain_model_package', 'EXT:tk_composer/Resources/Private/Language/locallang_csh_tx_tkcomposer_domain_model_package.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_tkcomposer_domain_model_package');
    }
);
## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder
$_EXTKEY = 'tk_composer';
