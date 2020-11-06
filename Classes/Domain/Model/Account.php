<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @package TimonKreis\TkComposer\Domain\Model
 */
class Account extends AbstractEntity
{
    /**
     * @var string
     */
    protected $username = '';

    /**
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     * @var string
     */
    protected $password = '';

    /**
     * @var bool
     */
    protected $allPackages = false;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TimonKreis\TkComposer\Domain\Model\Package>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $packages = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initializeObject();
    }

    /**
     * Initializes all ObjectStorage properties when model is reconstructed from DB (where __construct is not called)
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initializeObject() : void
    {
        $this->packages = $this->packages ?: new ObjectStorage();
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUsername() : string
    {
        return $this->username;
    }

    /**
     * Sets the username
     *
     * @param string $username
     * @return void
     */
    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    /**
     * Returns the password
     *
     * @return string
     */
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * Sets the password
     *
     * @param string $password
     * @return void
     */
    public function setPassword(string $password) : void
    {
        $this->password = $password;
    }

    /**
     * Returns the allPackages flag
     *
     * @return bool
     */
    public function getAllPackages() : bool
    {
        return $this->allPackages;
    }

    /**
     * Sets the allPackages flag
     *
     * @param bool $allPackages
     * @return void
     */
    public function setAllPackages(bool $allPackages) : void
    {
        $this->allPackages = $allPackages;
    }

    /**
     * Returns the boolean state of allPackages
     *
     * @return bool
     */
    public function isAllPackages() : bool
    {
        return $this->allPackages;
    }

    /**
     * Adds a Package
     *
     * @param Package $package
     * @return void
     */
    public function addPackage(Package $package) : void
    {
        $this->packages->attach($package);
    }

    /**
     * Removes a Package
     *
     * @param Package $packageToRemove The Package to be removed
     * @return void
     */
    public function removePackage(Package $packageToRemove) : void
    {
        $this->packages->detach($packageToRemove);
    }

    /**
     * Returns the packages
     *
     * @return ObjectStorage<Package>
     */
    public function getPackages() : ObjectStorage
    {
        return $this->packages;
    }

    /**
     * Sets the packages
     *
     * @param ObjectStorage<Package> $packages
     * @return void
     */
    public function setPackages(ObjectStorage $packages) : void
    {
        $this->packages = $packages;
    }
}
