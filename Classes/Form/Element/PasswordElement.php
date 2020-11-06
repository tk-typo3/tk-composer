<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Form\Element;

use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @package TimonKreis\TkComposer\Form\Element
 */
class PasswordElement extends InputTextElement
{
    /**
     * @inheritDoc
     */
    public function render() : array
    {
        $parent = parent::render();

        $parameterArray = $this->data['parameterArray'];
        $evalList = GeneralUtility::trimExplode(',', $parameterArray['fieldConf']['config']['eval'], true);

        $attributes = [
            'value' => '',
            'id' => StringUtility::getUniqueId('formengine-input-'),
            'class' => 'form-control t3js-clearable hasDefaultValue',
            'data-formengine-input-params' => json_encode([
                'field' => $parameterArray['itemFormElName'],
                'evalList' => implode(',', $evalList),
            ]),
            'data-formengine-input-name' => $parameterArray['itemFormElName'],
        ];

        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename('EXT:tk_composer/Resources/Private/Templates/Backend/Password.html');
        $standaloneView->assignMultiple([
            'attributes' => GeneralUtility::implodeAttributes($attributes, true),
            'name' => $parameterArray['itemFormElName'],
            'value' => htmlspecialchars($parameterArray['itemFormElValue']),
            'id' => $attributes['id'],
            'icon' => $this->iconFactory->getIcon('actions-synchronize', Icon::SIZE_SMALL)->render(),
        ]);

        $parent['html'] = $standaloneView->render();

        return $parent;
    }
}
