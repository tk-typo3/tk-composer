<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

defined('TYPO3_MODE') || die();

$tca = &$GLOBALS['TCA']['tx_tkcomposer_domain_model_account'];

// https://material.io/resources/icons/?icon=account_box&style=baseline
$tca['ctrl']['iconfile'] = 'EXT:tk_composer/Resources/Public/Icons/tx_tkcomposer_domain_model_account.svg';

$tca['columns']['username']['config']['size'] = 50;

// Hide fields for user
$tca['columns']['last_update']['config']['type'] = 'passthrough';
unset($tca['columns']['last_update']['config']['renderType']);

// Render type for password field
$tca['columns']['password']['config']['renderType'] = 'passwordWizard';

// Expand/collapse packages field and filter by private packages only
$tca['columns']['all_packages']['onChange'] = 'reload';
$tca['columns']['packages']['displayCond'] = 'FIELD:all_packages:=:0';
$tca['columns']['packages']['config']['foreign_table_where']
    = 'access = ' . TimonKreis\TkComposer\Domain\Model\Package::ACCESS_PRIVATE;
unset($tca['columns']['packages']['config']['size']);

// Group fields
TimonKreis\TkComposer\Tools\TCA\FieldsGroup::group($tca, ['username', 'hidden']);
TimonKreis\TkComposer\Tools\TCA\FieldsGroup::group($tca, ['password', 'all_packages']);
TimonKreis\TkComposer\Tools\TCA\FieldsGroup::group($tca, ['starttime', 'endtime']);
