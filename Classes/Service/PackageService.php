<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Service;

use Composer\Semver\Comparator;
use TimonKreis\TkComposer\Domain\Model\Package;
use TimonKreis\TkComposer\Domain\Repository\AccountRepository;
use TimonKreis\TkComposer\Domain\Repository\PackageRepository;
use TimonKreis\TkComposer\Exception;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * @package TimonKreis\TkComposer\Service
 */
class PackageService implements SingletonInterface
{
    /**
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var PackageRepository
     */
    protected $packageRepository;

    /**
     * @var FilesystemService
     */
    protected $filesystemService;

    /**
     * @var GitService
     */
    protected $gitService;

    /**
     * @param PersistenceManagerInterface $persistenceManager
     * @param AccountRepository $accountRepository
     * @param PackageRepository $packageRepository
     * @param FilesystemService $filesystemService
     * @param GitService $gitService
     */
    public function __construct(
        PersistenceManagerInterface $persistenceManager,
        AccountRepository $accountRepository,
        PackageRepository $packageRepository,
        FilesystemService $filesystemService,
        GitService $gitService
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->accountRepository = $accountRepository;
        $this->packageRepository = $packageRepository;
        $this->filesystemService = $filesystemService;
        $this->gitService = $gitService;
    }

    /**
     * @param Package $package
     * @param bool $forceFullReload
     * @return bool
     * @throws \Exception
     */
    public function updatePackage(Package $package, bool $forceFullReload = false) : bool
    {
        // Pull recent changes for cloned repositories
        if ($package->getRelation() == Package::RELATION_CLONED) {
            $this->gitService->pullRepositoryByPackage($package);
        }

        // New packages are not related to their repository, neither directly nor cloned
        if ($package->getRelation() == Package::RELATION_UNDEFINED) {
            $repositoryRootPath = $this->filesystemService->getRepositoryRootPath();
            $relativePath = $this->filesystemService->getRelativePathByRepositoryUrl($package->getRepositoryUrl());

            // Check if path exists
            if ($repositoryRootPath
                && (@is_dir($repositoryRootPath . $relativePath) || @is_link($repositoryRootPath . $relativePath))
            ) {
                // Package is directly related, nothing else to do here
                $package->setRelation(Package::RELATION_DIRECT);
            } else {
                $package->setRelation(Package::RELATION_CLONED);

                $this->gitService->cloneRepositoryByPackage($package);
            }
        }

        $tags = $this->gitService->getTagsAndCommitIdsByPackage($package);
        $tagsStatus = $package->getTagsStatus();
        $changes = false;

        if ($tags) {
            $storagePath = $this->filesystemService->getStoragePathByPackage($package) . 'tags/';

            foreach ($tags as $tag => $commitId) {
                $storageFile = $storagePath . md5($tag);

                // Reload data if...
                //  ...a full reload is forced
                //  ...the tag does not exist
                //  ...the stored commit id does not match the tags commit id
                //  ...the stored file does not exist
                if ($forceFullReload
                    || !isset($tagsStatus[$tag])
                    || ($tagsStatus[$tag] && $tagsStatus[$tag] != $commitId)
                    || ($tagsStatus[$tag] && !@is_file($storageFile))
                ) {
                    $changes = true;
                    $composer = $this->gitService->getEnrichedComposerJsonByPackageAndTag($package, $tag);

                    // Skip tags without a `composer.json` or without a composer name
                    if (!$composer || !isset($composer['name'])) {
                        $tagsStatus[$tag] = false;

                        continue;
                    }

                    $tagsStatus[$tag] = $commitId;

                    // Check if the tag is a newer version than the previously latest tag
                    if (!$package->getLatestTag() || Comparator::greaterThan($tag, $package->getLatestTag())) {
                        $package->setLatestTag($tag);

                        // Update package name and description to reflect latest changes
                        $package->setPackageName($composer['name']);
                        $package->setDescription($composer['description'] ?? '');
                    }

                    // Write JSON data to tags file
                    try {
                        $this->filesystemService->setContentOfFile(
                            $storageFile,
                            json_encode($composer, JSON_UNESCAPED_SLASHES)
                        );
                    } catch (\Exception $e) {
                        /** @see FilesystemService::setContentOfFile() */
                        if ($e->getCode() == 1602366654) {
                            throw new Exception(
                                [
                                    'Unable to create storage directory for tag "%s" of package "%s"',
                                    $tag,
                                    $package->getRepositoryUrl(),
                                ],
                                1602367398
                            );
                        } else {
                            throw $e;
                        }
                    }
                }
            }
        } elseif ($tagsStatus) {
            // Reset tags status because repository has no tags any more but had them in the past
            $tagsStatus = [];
        }

        $package->setTagsStatus($tagsStatus);

        // Create the JSON for the whole package, if it has any changes
        if ($changes) {
            $storageFile = $this->filesystemService->getStoragePathByPackage($package) . 'package';
            $isFirst = true;

            try {
                $this->filesystemService->setContentOfFile(
                    $storageFile,
                    '{"packages":{"' . $package->getPackageName() . '":{'
                );

                foreach ($tagsStatus as $tag => $commitId) {
                    // Skip non-composer tags
                    if (!$commitId) {
                        continue;
                    }

                    $comma = $isFirst ? '' : ',';
                    $isFirst = false;

                    $storagePath = $this->filesystemService->getStoragePathByPackage($package) . 'tags/';
                    $tagContent = $this->filesystemService->getContentOfFile($storagePath . md5($tag));

                    $this->filesystemService->setContentOfFile(
                        $storageFile,
                        $comma . '"' . $tag . '":' . $tagContent,
                        true
                    );
                }

                $this->filesystemService->setContentOfFile($storageFile, '}}}', true);
            } catch (\Exception $e) {
                /** @see FilesystemService::setContentOfFile() */
                if ($e->getCode() == 1602366654) {
                    throw new Exception(
                        [
                            'Unable to create storage directory for package "%s"',
                            $package->getRepositoryUrl(),
                        ],
                        1602367439
                    );
                } else {
                    throw $e;
                }
            }

            $hash = hash_file('sha256', $storageFile);

            $package->setHash($hash);
        }

        // Skip persisting the package if nothing was changed and return false
        if (!$package->_isDirty()) {
            return false;
        }

        $this->packageRepository->update($package);
        $this->persistenceManager->persistAll();

        return true;
    }

    /**
     * Update all packages
     *
     * @param bool $forceFullReload
     * @throws \Exception
     */
    public function updateAllPackages(bool $forceFullReload = false) : void
    {
        $packages = $this->packageRepository->findAll();

        /** @var Package $package */
        foreach ($packages as $package) {
            try {
                $this->updatePackage($package, $forceFullReload);
            } catch(\Exception $e) {}
        }
    }
}
