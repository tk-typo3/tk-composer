<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * @package TimonKreis\TkComposer\Domain\Repository
 */
class PackageGroupRepository extends AbstractRepository
{
    public const TABLE = 'tx_tkcomposer_domain_model_packagegroup';

    /**
     * @var array
     */
    protected $defaultOrdering = [
        'name' => QueryInterface::ORDER_ASCENDING,
    ];
}
