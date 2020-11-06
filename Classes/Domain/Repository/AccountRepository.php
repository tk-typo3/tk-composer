<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Domain\Repository;

use TimonKreis\TkComposer\Domain\Model\Account;
use TimonKreis\TkComposer\Service\AccountService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package TimonKreis\TkComposer\Domain\Repository
 */
class AccountRepository extends AbstractRepository
{
    public const TABLE = 'tx_tkcomposer_domain_model_account';

    /**
     * Find by username and password
     *
     * @param string $username
     * @param string $password
     * @return Account|null
     */
    public function findByUsernameAndPassword(string $username, string $password) : ?Account
    {
        $queryBuilder = $this->getQueryBuilder();

        $query = $queryBuilder
            ->select('*')
            ->where($queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($username)))
            ->andWhere($queryBuilder->expr()->eq('password', $queryBuilder->createNamedParameter($password)))
            ->setMaxResults(1);

        $account = $this->getMappedData(Account::class, $query->execute()->fetchAll());

        return $account[0] ?? null;
    }

    /**
     * Find by username and password hash
     *
     * @param string $username
     * @param string $passwordHash
     * @return Account|null
     */
    public function findByUsernameAndPasswordHash(string $username, string $passwordHash) : ?Account
    {
        $queryBuilder = $this->getQueryBuilder();

        $query = $queryBuilder
            ->select('*')
            ->where($queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($username)))
            ->setMaxResults(1);

        $account = $this->getMappedData(Account::class, $query->execute()->fetchAll());

        if (!$account) {
            return null;
        }

        /** @var AccountService $accountService */
        $accountService = GeneralUtility::makeInstance(AccountService::class);

        /** @var Account $account */
        $account = $account[0];

        return $accountService->getPasswordHashByPassword($account->getPassword()) == $passwordHash ? $account : null;
    }
}
