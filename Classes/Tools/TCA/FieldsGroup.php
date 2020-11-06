<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Tools\TCA;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package TimonKreis\TkComposer\Tools\TCA
 */
class FieldsGroup
{
    /**
     * Group TCA fields in one row
     *
     * @param array $tca
     * @param array $fields
     * @param string $label
     * @param string $palette
     */
    public static function group(array &$tca, array $fields, string $label = '', string $palette = '') : void
    {
        if ($palette == '') {
            $palette = implode('__', $fields);
        }

        $tca['palettes'][$palette]['showitem'] = implode(', ', $fields);

        if ($label != '') {
            $tca['palettes'][$palette]['label'] = $label;
        }

        $paletteInserted = false;
        $availableFields = GeneralUtility::trimExplode(',', $tca['types']['1']['showitem']);
        $filteredFields = [];

        foreach ($availableFields as $field) {
            if (in_array($field, $fields)) {
                if (!$paletteInserted) {
                    $paletteInserted = true;

                    $filteredFields[] = '--palette--;;' . $palette;
                }
            } else {
                $filteredFields[] = $field;
            }
        }

        $tca['types']['1']['showitem'] = implode(', ', $filteredFields);
    }
}
