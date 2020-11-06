<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_account',
        'label' => 'username',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'username,password',
        'iconfile' => 'EXT:tk_composer/Resources/Public/Icons/tx_tkcomposer_domain_model_account.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, username, password, all_packages, packages',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, username, password, all_packages, packages, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'username' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_account.username',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'password' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_account.password',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'all_packages' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_account.all_packages',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
                'default' => 0,
            ]
        ],
        'packages' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_account.packages',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_tkcomposer_domain_model_package',
                'MM' => 'tx_tkcomposer_account_package_mm',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false,
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => true,
                    ],
                ],
            ],

        ],

    ],
];
