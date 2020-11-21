<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Service;

use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use TimonKreis\TkComposer\Domain\Model\Package;
use TimonKreis\TkComposer\Exception;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package TimonKreis\TkComposer\Service
 */
class GitService implements SingletonInterface
{
    /**
     * @var FilesystemService
     */
    protected $filesystemService;

    /**
     * @var CommandService
     */
    protected $commandService;

    /**
     * @param FilesystemService $filesystemService
     * @param CommandService $commandService
     */
    public function __construct(FilesystemService $filesystemService, CommandService $commandService)
    {
        $this->filesystemService = $filesystemService;
        $this->commandService = $commandService;
    }

    /**
     * Clone repository
     *
     * @param Package $package
     * @throws \Exception
     */
    public function cloneRepositoryByPackage(Package $package) : void
    {
        try {
            // Create bare clone of repository
            /** @see https://git-scm.com/docs/git-clone */
            $this->commandService->run(
                'git clone --bare %s %s',
                $package->getRepositoryUrl(),
                $this->filesystemService->getRepositoryPathByPackage($package)
            );
        } catch (\Exception $e) {
            throw new Exception(['Unable to clone repository "%s"', $package->getRepositoryUrl()], 1602366892);
        }
    }

    /**
     * Pull repository
     *
     * @param Package $package
     * @throws \Exception
     */
    public function pullRepositoryByPackage(Package $package) : void
    {
        try {
            // Receive changes
            /** @see https://git-scm.com/docs/git-pull */
            $this->commandService->run(
                'cd %s && git pull origin master',
                $this->filesystemService->getRepositoryPathByPackage($package)
            );
        } catch (\Exception $e) {
            throw new Exception(['Unable to pull repository "%s"', $package->getRepositoryUrl()], 1602366908);
        }
    }

    /**
     * Get tags and commit ids for package in ascending order
     *
     * @param Package $package
     * @return string[]
     * @throws \Exception
     */
    public function getTagsAndCommitIdsByPackage(Package $package) : array
    {
        try {
            // Receive list of tags and their commit ids
            /** @see https://git-scm.com/docs/git-show-ref */
            $response = $this->commandService->run(
                'cd %s && git show-ref',
                $this->filesystemService->getRepositoryPathByPackage($package)
            );

            $tags = [];
            $commitIds = [];

            foreach (array_filter($response) as $row) {
                if (!strpos($row, 'refs/tags/')) {
                    continue;
                }

                [$commitId, $tag] = preg_split('/\s+refs\/tags\//', $row);

                $tags[] = $tag;
                $commitIds[$tag] = $commitId;
            }

            $tags = Semver::sort($tags);
            $tags = array_flip($tags);

            foreach ($tags as $tag => &$commitId) {
                $commitId = $commitIds[$tag];
            }

            // Receive latest commit id
            /** @see https://git-scm.com/docs/git-tag */
            $latestCommitId = $this->commandService->run(
                'cd %s && git rev-parse HEAD',
                $this->filesystemService->getRepositoryPathByPackage($package)
            );

            $tags['dev-master'] = $latestCommitId[0];
        } catch (\Exception $e) {
            throw new Exception(['Unable to fetch tags for repository "%s"', $package->getRepositoryUrl()], 1602366922);
        }

        return $tags;
    }

    /**
     * Get enriched `composer.json` by package and tag
     *
     * @param Package $package
     * @param string $tag
     * @return array|null
     * @throws \Exception
     */
    public function getEnrichedComposerJsonByPackageAndTag(Package $package, string $tag) : ?array
    {
        $sanitizedTag = $this->getSanitizedTag($tag);

        try {
            // Get `composer.json` for specific tag
            /** @see https://git-scm.com/docs/git-show */
            $composer = $this->commandService->run(
                'cd %s && git show %s:composer.json 2> /dev/null',
                $this->filesystemService->getRepositoryPathByPackage($package),
                $sanitizedTag
            );
        } catch (\Exception $e) {
            return null;
        }

        try {
            // Get commit id and timestamp for tag
            /** @see https://git-scm.com/docs/git-show#_pretty_formats */
            $metadata = $this->commandService->run(
                'cd %s && git show -s --format="%%H%%n%%ct" %s',
                $this->filesystemService->getRepositoryPathByPackage($package),
                $sanitizedTag
            );

            if (!$metadata) {
                throw new \Exception();
            }

            [$commitId, $timestamp] = $metadata;
        } catch (\Exception $e) {
            throw new Exception(
                [
                    'Unable to determine commit id and timestamp in repository "%s" for tag "%s"',
                    $package->getRepositoryUrl(),
                    $tag,
                ],
                1602366941
            );
        }

        $composer = implode("\n", array_map('trim', $composer));
        $composer = json_decode($composer, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new Exception([json_last_error_msg()], 1602367002);
        }

        $versionParser = new VersionParser();

        // Enrich data
        $composer['version'] = $tag;
        $composer['version_normalized'] = $versionParser->normalize($tag);
        $composer['time'] = date('c', (int)$timestamp);
        $composer['source'] = [
            'type' => $package->getReadableType(),
            'url' => $package->getRepositoryUrl(),
            'reference' => $commitId,
        ];

        return $composer;
    }

    /**
     * Get sanitized tag
     *
     * @param string $tag
     * @return string
     */
    protected function getSanitizedTag(string $tag) : string
    {
        return $tag == 'dev-master' ? 'HEAD' : $tag;
    }
}
