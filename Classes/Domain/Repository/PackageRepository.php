<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Domain\Repository;

use Doctrine\DBAL\ParameterType;
use TimonKreis\TkComposer\Domain\Model\Account;
use TimonKreis\TkComposer\Domain\Model\Package;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * @package TimonKreis\TkComposer\Domain\Repository
 */
class PackageRepository extends AbstractRepository
{
    public const TABLE = 'tx_tkcomposer_domain_model_package';

    /**
     * @var array
     */
    protected $defaultOrdering = [
        'package_name' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Find packages by account
     *
     * @param Account|null $account
     * @return Package[]
     */
    public function findByAccount(Account $account = null) : array
    {
        $queryBuilder = $this->getQueryBuilder();
        $query = $queryBuilder->select('*');

        if ($account) {
            if (!$account->getAllPackages()) {
                $query->where(
                    $queryBuilder->expr()->eq(
                        'access',
                        $queryBuilder->createNamedParameter(Package::ACCESS_PROTECTED, ParameterType::INTEGER)
                    )
                )->orWhere(
                    $queryBuilder->expr()->eq(
                        'access',
                        $queryBuilder->createNamedParameter(Package::ACCESS_PUBLIC, ParameterType::INTEGER)
                    )
                );

                $packageUids = [];

                /** @var Package $package */
                foreach ($account->getPackages() as $package) {
                    $packageUids[] = $queryBuilder->createNamedParameter($package->getUid(), ParameterType::INTEGER);
                }

                if ($packageUids) {
                    $query->orWhere($queryBuilder->expr()->in('uid', $packageUids));
                }
            }
        } else {
            $query->where(
                $queryBuilder->expr()->eq(
                    'access',
                    $queryBuilder->createNamedParameter(Package::ACCESS_PUBLIC, ParameterType::INTEGER)
                )
            );
        }

        return $this->getMappedData(Package::class, $query->execute()->fetchAll());
    }

    /**
     * Find by package name
     *
     * @param string $name
     * @return Package|null
     */
    public function findByPackageName(string $name) : ?Package
    {
        $queryBuilder = $this->getQueryBuilder();

        $query = $queryBuilder
            ->select('*')
            ->where($queryBuilder->expr()->eq('package_name', $queryBuilder->createNamedParameter($name)))
            ->setMaxResults(1);

        $package = $this->getMappedData(Package::class, $query->execute()->fetchAll());

        return $package[0] ?? null;
    }
}
