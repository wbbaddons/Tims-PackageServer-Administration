<?php
namespace wcf\acp\page;

use wcf\data\user\group\UserGroup;
use wcf\system\WCF;

/**
 * Represents a list of all premium-groups.
 * 
 * @author	Joshua Rüsweg
 * @package	de.joshsboard.jcoins
 * @subpackage	acp.page
 */
class PermissionOverviewPage extends \wcf\page\AbstractPage {

	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.permissionOverview';
	
	public $items = array(); 
	
	public function readData() {
		parent::readData();
		
		// read first all general permissions
		$stmt = WCF::getDB()->prepareStatement("SELECT *, 'general' as type FROM wcf". WCF_N ."_packageserver_package_permission_general");
		$stmt->execute();
		
		while ($row = $stmt->fetchArray()) {
			$this->items[] = $row; 
		}
		
		$stmt = WCF::getDB()->prepareStatement("SELECT *, 'user' as type FROM wcf". WCF_N ."_packageserver_package_to_user");
		$stmt->execute();
		
		while ($row = $stmt->fetchArray()) {
			$this->items[] = $row;
		}
		
		$stmt = WCF::getDB()->prepareStatement("SELECT *, 'group' as type FROM wcf". WCF_N ."_packageserver_package_to_group");
		$stmt->execute();
		
		while ($row = $stmt->fetchArray()) {
			$this->items[] = $row; 
		}
	}
	
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'items' => $this->items
		));
	}
}