<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package',
        'label' => 'repository_url',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'repository_url,hash,latest_tag,package_name,description,tags_status',
        'iconfile' => 'EXT:tk_composer/Resources/Public/Icons/tx_tkcomposer_domain_model_package.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, repository_url, type, access, relation, hash, latest_tag, package_name, description, tags_status',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, repository_url, type, access, relation, hash, latest_tag, package_name, description, tags_status, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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

        'repository_url' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.repository_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'type' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['-- Label --', 0],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ],
        ],
        'access' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.access',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['-- Label --', 0],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ],
        ],
        'relation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.relation',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'hash' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.hash',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'latest_tag' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.latest_tag',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'package_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.package_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'tags_status' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tk_composer/Resources/Private/Language/locallang_db.xlf:tx_tkcomposer_domain_model_package.tags_status',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],

    ],
];
