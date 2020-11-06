<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright by Timon Kreis - All rights reserved
 * @license https://www.gnu.org/licenses/gpl-3.0.de.html
 */
declare(strict_types = 1);

namespace TimonKreis\TkComposer;

/**
 * @package TimonKreis\TkComposer
 */
class Exception extends \TYPO3\CMS\Core\Exception
{
    /**
     * @param array $message
     * @param int $code
     */
    public function __construct(array $message, int $code = 0)
    {
        $message = count($message) > 1 ? vsprintf(array_shift($message), $message) : ($message[0] ?? '');

        parent::__construct($message, $code);
    }
}
