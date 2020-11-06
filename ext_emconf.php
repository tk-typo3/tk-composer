<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

/**
 * @var $_EXTKEY
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Composer',
    'description' => 'Serve composer packages to authorized accounts.',
    'category' => 'services',
    'author' => 'Timon Kreis',
    'author_email' => 'mail@timonkreis.de',
    'state' => 'stable',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
