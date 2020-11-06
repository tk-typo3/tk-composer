<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Service;

use TimonKreis\TkComposer\Domain\Model\Account;
use TimonKreis\TkComposer\Domain\Repository\AccountRepository;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package TimonKreis\TkComposer\Service
 */
class AccountService implements SingletonInterface
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    /**
     * Get authorized account
     *
     * @return Account
     */
    public function getAuthorizedAccount() : ?Account
    {
        if (isset($_COOKIE['auth'])) {
            [$username, $passwordHash] = explode(':', $_COOKIE['auth'], 2);

            return $this->accountRepository->findByUsernameAndPasswordHash($username, $passwordHash);
        }

        if (!isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
            return null;
        }

        $username = trim($_SERVER['PHP_AUTH_USER']);
        $password = trim($_SERVER['PHP_AUTH_PW']);

        if ($username == '' || $password == '') {
            return null;
        }

        return $this->accountRepository->findByUsernameAndPassword($username, $password);
    }

    /**
     * Get password hash by password
     *
     * @param string $password
     * @return string
     */
    public function getPasswordHashByPassword(string $password): string
    {
        return hash('sha256', $password);
    }
}
