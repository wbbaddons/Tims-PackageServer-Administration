<?php

namespace wcf\system\event\listener;

use wcf\util\PackageServerUtil;

/**
 * Updates the authentication file of the PackageServer once a users’ information is updated.
 *
 * @author  Joshua Rüsweg
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package be.bastelstu.josh.ps
 */
final class PackageServerUserChangeListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!PACKAGESERVER_BUILDAUTH) {
            return;
        }

        $parameters = $eventObj->getParameters();
        switch ($eventObj->getActionName()) {
            case 'update':
            case 'create':
            case 'addToGroups':
                PackageServerUtil::generateAuthFile();
                break;
        }
    }
}
