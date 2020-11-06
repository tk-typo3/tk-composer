<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer\Service;

use TimonKreis\TkComposer\Exception;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package TimonKreis\TkComposer\Service
 */
class CommandService implements SingletonInterface
{
    /**
     * Execute command and return response as array
     *
     * @param string $command
     * @param string ...$parameters
     * @return array
     * @throws \Exception
     */
    public function run(string $command, string ...$parameters) : array
    {
        $response = [];
        $command = $parameters ? vsprintf($command, $parameters) : $command;

        // Execute command
        exec($command, $response, $exitCode);

        if ($exitCode) {
            throw new Exception(['Unable to execute command "%s"', $command], 1602366776);
        }

        return $response;
    }
}
