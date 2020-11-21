<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

defined('TYPO3_MODE') || die();

$tca = &$GLOBALS['TCA']['tx_tkcomposer_domain_model_packagegroup'];

// https://material.io/resources/icons/?icon=group_work&style=baseline
$tca['ctrl']['iconfile'] = 'EXT:tk_composer/Resources/Public/Icons/tx_tkcomposer_domain_model_packagegroup.svg';

// Modify name size
$tca['columns']['name']['config']['size'] = 40;

$tca['columns']['packages']['config']['foreign_table_where']
    = 'access = ' . TimonKreis\TkComposer\Domain\Model\Package::ACCESS_PRIVATE;
unset($tca['columns']['packages']['config']['size']);

// Add account assignment to package group side
$tca['interface']['showRecordFieldList'] .= ', accounts';
$tca['types']['1']['showitem'] = str_replace('hidden, ', 'hidden,  accounts,', $tca['types']['1']['showitem']);
$tca['columns']['accounts'] = [
    'exclude' => true,
    'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_packagegroup.accounts',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectMultipleSideBySide',
        'foreign_table' => 'tx_tkcomposer_domain_model_account',
        'foreign_table_where' => 'all_packages = 0',
        'MM' => 'tx_tkcomposer_account_packagegroup_mm',
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
TimonKreis\TkComposer\Tools\TCA\FieldsGroup::group($tca, ['name', 'hidden']);
TimonKreis\TkComposer\Tools\TCA\FieldsGroup::group($tca, ['starttime', 'endtime']);
