<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Service;

use TimonKreis\TkComposer\Domain\Model\Package;
use TimonKreis\TkComposer\Exception;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package TimonKreis\TkComposer\Service
 */
class FilesystemService implements SingletonInterface
{
    /**
     * @var ExtensionConfiguration
     */
    protected $extensionConfiguration;

    /**
     * @param ExtensionConfiguration $extensionConfiguration
     */
    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    /**
     * Get configured storage path
     *
     * @return string
     * @throws \Exception
     */
    public function getStorageRootPath() : string
    {
        static $path = null;

        if ($path === null) {
            $path = $this->extensionConfiguration->get('tk_composer', 'basic/storagePath');

            if (!$path) {
                throw new Exception(['Storage path is not defined in extension configuration'], 1602366696);
            }

            $path = $this->getAbsolutePathByRelativePath($path);
        }

        return $path;
    }

    /**
     * Get storage path by package
     *
     * @param Package $package
     * @return string
     * @throws \Exception
     */
    public function getStoragePathByPackage(Package $package) : string
    {
        return $this->getStorageRootPath() . 'packages/p' . $package->getUid() . '/';
    }

    /**
     * Get repository root path
     *
     * @return string|null
     * @throws \Exception
     */
    public function getRepositoryRootPath() : ?string
    {
        static $path = null;

        if ($path === null) {
            $path = $this->extensionConfiguration->get('tk_composer', 'basic/repositoryRootPath');

            if ($path) {
                $path = $this->getAbsolutePathByRelativePath($path);

                if (!@is_dir($path) && !@is_link($path)) {
                    throw new Exception(['Repository root path is defined but does not exist'], 1602366677);
                }
            } else {
                $path = '';
            }
        }

        return $path;
    }

    /**
     * Get resolved path to repository (direct or cloned)
     *
     * @param Package $package
     * @return string
     * @throws \Exception
     */
    public function getRepositoryPathByPackage(Package $package) : string
    {
        $relativePath = $this->getRelativePathByRepositoryUrl($package->getRepositoryUrl());

        return $package->getRelation() == Package::RELATION_DIRECT
            ? $this->getRepositoryRootPath() . $relativePath
            : $this->getStorageRootPath() . 'repositories/r' . $package->getUid() . '/';
    }

    /**
     * Get relative path by repository URL
     *
     * @param string $repositoryUrl
     * @return string
     */
    public function getRelativePathByRepositoryUrl(string $repositoryUrl) : string
    {
        $path = strpos($repositoryUrl, '//') !== false
            ? explode('/', $repositoryUrl, 4)[3]
            : explode(':', $repositoryUrl)[1];

        return rtrim($path, '/') . '/';
    }

    /**
     * Get content of file
     *
     * @param string $file
     * @return string
     * @throws Exception
     */
    public function getContentOfFile(string $file) : string
    {
        $content = @file_get_contents($file);

        if ($content === false) {
            throw new Exception(['Unable to read content of file "%s"', $file], 1602368333);
        }

        return $content;
    }

    /**
     * Set content of file
     *
     * @param string $file
     * @param string $content
     * @param bool $append
     * @throws \Exception
     */
    public function setContentOfFile(string $file, string $content, bool $append = false) : void
    {
        $directory = dirname($file);

        // Create directory if missing
        if (!@is_dir($directory) && !@mkdir($directory, 0777, true)) {
            throw new Exception(['Unable to create directory for file "%s"', $file], 1602366654);
        }

        // Write content to file
        if (!@file_put_contents($file, $content, $append ? FILE_APPEND : 0)) {
            throw new Exception(['Unable to write content to file "%s"', $file], 1602366663);
        }
    }

    /**
     * Get absolute by relative path (from project root)
     *
     * @param string $path
     * @return string
     */
    protected function getAbsolutePathByRelativePath(string $path) : string
    {
        if (strpos($path, '/') !== 0) {
            $path = Environment::getPublicPath() . '/' . $path;
        }

        return rtrim($path, '/') . '/';
    }
}
