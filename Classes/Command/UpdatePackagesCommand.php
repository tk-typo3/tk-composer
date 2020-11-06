<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TimonKreis\TkComposer\Service\PackageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package TimonKreis\TkComposer\Command
 */
class UpdatePackagesCommand extends Command
{
    /**
     * @var PackageService
     */
    protected $packageService;

    /**
     * @inheritDoc
     */
    protected function configure() : void
    {
        $this->setDescription('Update packages')
            ->setHelp('Updates all packages in database.')
            ->addOption('force-reload', 'f', InputOption::VALUE_NONE, 'Force full reload of all packages');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->packageService = GeneralUtility::makeInstance(PackageService::class);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Updating all packages');

        try {
            $forceReload = $input->getOption('force-reload') || is_string($input->getOption('force-reload'));

            if ($forceReload) {
                $io->note('Running force reload');
            }

            $this->packageService->updateAllPackages($forceReload);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return $e->getCode() ? $e->getCode() : 1;
        }

        $io->success('Packages sucessfully updated');

        return 0;
    }
}
