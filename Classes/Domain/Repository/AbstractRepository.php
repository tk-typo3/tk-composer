<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @package TimonKreis\TkComposer\Domain\Repository
 */
abstract class AbstractRepository extends Repository
{
    public const TABLE = '';

    /**
     * @var DataMapper
     */
    protected $dataMapper;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param DataMapper $dataMapper
     */
    public function __construct(ObjectManagerInterface $objectManager, DataMapper $dataMapper)
    {
        parent::__construct($objectManager);

        $this->dataMapper = $dataMapper;
    }

    /**
     * @return void
     */
    public function initializeObject() : void
    {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);

        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Get query builder
     *
     * @param string|null $table
     * @param string|null $alias
     * @return QueryBuilder
     */
    protected function getQueryBuilder(string $table = null, string $alias = null) : QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table ?? $this::TABLE)
            ->from($table ?? $this::TABLE, $alias);
    }

    /**
     * Get mapped data
     *
     * @param string $className
     * @param array $rows
     * @return array
     */
    protected function getMappedData(string $className, array $rows) : array
    {
        return $this->dataMapper->map($className, $rows);
    }
}
