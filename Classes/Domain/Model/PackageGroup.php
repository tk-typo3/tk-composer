<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types=1);

namespace TimonKreis\TkComposer\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @package TimonKreis\TkComposer\Domain\Model
 */
class PackageGroup extends AbstractEntity
{
    /**
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     * @var string
     */
    protected $name = '';

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TimonKreis\TkComposer\Domain\Model\Package>
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
     * Returns the name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
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
