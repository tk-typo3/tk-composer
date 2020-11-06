<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

defined('TYPO3_MODE') || die();

$tca = &$GLOBALS['TCA']['tx_tkcomposer_domain_model_package'];

// https://material.io/resources/icons/?icon=archive&style=baseline
$tca['ctrl']['iconfile'] = 'EXT:tk_composer/Resources/Public/Icons/tx_tkcomposer_domain_model_package.svg';

// Modify repository URL field
$tca['columns']['repository_url']['config']['size'] = 40;
$tca['columns']['repository_url']['config']['max'] = 190;

// Add type options
$tca['columns']['type']['config']['items'] = [
    [
        'LLL:EXT:tk_composer/Resources/Private/Language/locallang.xlf:type.git',
        TimonKreis\TkComposer\Domain\Model\Package::TYPE_GIT,
    ],
];

// Add access options
$tca['columns']['access']['config']['default'] = TimonKreis\TkComposer\Domain\Model\Package::ACCESS_PRIVATE;
$tca['columns']['access']['onChange'] = 'reload';
$tca['columns']['access']['config']['items'] = [
    [
        'LLL:EXT:tk_composer/Resources/Private/Language/locallang.xlf:access.private',
        TimonKreis\TkComposer\Domain\Model\Package::ACCESS_PRIVATE,
    ],
    [
        'LLL:EXT:tk_composer/Resources/Private/Language/locallang.xlf:access.protected',
        TimonKreis\TkComposer\Domain\Model\Package::ACCESS_PROTECTED,
    ],
    [
        'LLL:EXT:tk_composer/Resources/Private/Language/locallang.xlf:access.public',
        TimonKreis\TkComposer\Domain\Model\Package::ACCESS_PUBLIC,
    ],
];

// Hide fields for user
$tca['columns']['relation']['config']['type'] = 'passthrough';
$tca['columns']['hash']['config']['type'] = 'passthrough';
$tca['columns']['latest_tag']['config']['type'] = 'passthrough';
$tca['columns']['package_name']['config']['type'] = 'passthrough';
$tca['columns']['description']['config']['type'] = 'passthrough';
$tca['columns']['tags_status']['config']['type'] = 'passthrough';

// Add account assignment to package side
$tca['interface']['showRecordFieldList'] .= ', accounts';
$tca['types']['1']['showitem'] = str_replace(' access,', ' access, accounts,', $tca['types']['1']['showitem']);
$tca['columns']['accounts'] = [
    'exclude' => true,
    'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.accounts',
    'displayCond' => 'FIELD:access:=:' . TimonKreis\TkComposer\Domain\Model\Package::ACCESS_PRIVATE,
    'config' => [
        'type' => 'select',
        'renderType' => 'selectMultipleSideBySide',
        'foreign_table' => 'tx_tkcomposer_domain_model_account',
        'foreign_table_where' => 'all_packages = 0',
        'MM' => 'tx_tkcomposer_account_package_mm',
        'MM_opposite_field' => 'packages',
        'autoSizeMax' => 30,
        'maxitems' => 9999,
        'multiple' => 0,
        'fieldControl' => [
            'addRecord' => [
                'disabled' => false,
            ],
            'listModule' => [
                'disabled' => true,
            ],
        ],
    ],
];

// Group fields
TimonKreis\TkComposer\Tools\TCA\FieldsGroup::group($tca, ['repository_url', 'hidden']);
TimonKreis\TkComposer\Tools\TCA\FieldsGroup::group($tca, ['type', 'access']);
TimonKreis\TkComposer\Tools\TCA\FieldsGroup::group($tca, ['starttime', 'endtime']);
