<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

return [
    'frontend' => [
        'tk-typo3/tk-composer' => [
            'target' => TimonKreis\TkComposer\Middleware\Frontend::class,
            'before' => ['typo3/cms-frontend/backend-user-authentication'],
        ],
    ],
];
