<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\util\PackageServerUtil;

/**
 * Updates the authentication file of the PackageServer once a group’ information is updated.
 *
 * @author		Joshua Rüsweg
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		be.bastelstu.josh.ps
 */
class PackageServerGroupChangeListener implements IEventListener {

	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!PACKAGESERVER_BUILDAUTH) return;
		
		PackageServerUtil::generateAuthFile();
	}

}
