<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

defined('TYPO3_MODE') || die();

$tca = &$GLOBALS['TCA']['tx_tkcomposer_domain_model_package'];

// https://material.io/resources/icons/?icon=archive&style=baseline
$tca['ctrl']['iconfile'] = 'EXT:tk_composer/Resources/Public/Icons/tx_tkcomposer_domain_model_package.svg';

// Sort entries
$tca['ctrl']['default_sortby'] = 'repository_url ASC';

// Modify repository URL field
$tca['columns']['repository_url']['config']['size'] = 50;
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

// Add package group assignment to package side
$tca['interface']['showRecordFieldList'] .= ', package_groups';
$tca['types']['1']['showitem'] = str_replace(' access,', ' access, package_groups,', $tca['types']['1']['showitem']);
$tca['columns']['package_groups'] = [
    'exclude' => true,
    'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.package_groups',
    'displayCond' => 'FIELD:access:=:' . TimonKreis\TkComposer\Domain\Model\Package::ACCESS_PRIVATE,
    'config' => [
        'type' => 'select',
        'renderType' => 'selectMultipleSideBySide',
        'foreign_table' => 'tx_tkcomposer_domain_model_packagegroup',
        'MM' => 'tx_tkcomposer_packagegroup_package_mm',
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

// Add account assignment to package side
$tca['interface']['showRecordFieldList'] .= ', accounts';
$tca['types']['1']['showitem'] = str_replace(' package_groups,', ' package_groups, accounts,', $tca['types']['1']['showitem']);
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
