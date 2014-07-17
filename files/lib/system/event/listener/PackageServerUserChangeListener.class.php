<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\util\PackageServerUtil; 

/**
 * Changes the auth.json-file for the PackageServer when a user is changed.
 * 
 * @author		Joshua Rüsweg
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		be.bastelstu.josh.ps
 */
class PackageServerUserChangeListener implements IEventListener {

	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!PACKAGESERVER_BUILDAUTH) return; 
		
		$parameters = $eventObj->getParameters(); 
		
		switch ($eventObj->getActionName()) {
			case 'update':
				// if the user or an administrator change the username
				// we should replace the whole file so we make sure
				// that the old username username is invalid
				// we should change this in the future to an efficient method
				if (isset($parameters['data']['username'])) {
					PackageServerUtil::generateAuthFile();
				}
				else if (isset($parameters['data']['removeGroups']) || isset($parameters['data']['password'])) {
					foreach ($eventObj->getObjects() as $user) {
						PackageServerUtil::updateUserAuth($user->getDecoratedObject());
					}
				}
				break; 
				
			case 'create': 
			case 'addToGroups':
				foreach ($eventObj->getObjects() as $user) {
					PackageServerUtil::updateUserAuth($user->getDecoratedObject());
				}
				break; 
				
			case 'delete':
				// generating a completely new file
				// we should change this in the future
				PackageServerUtil::generateAuthFile(); 
				break;
		}
	}

}
