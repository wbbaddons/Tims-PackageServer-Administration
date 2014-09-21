<?php
namespace wcf\acp\page;

use wcf\data\user\group\UserGroup;
use wcf\system\WCF;

/**
 * Represents a list of all permissions
 *
 * @author		Joshua Rüsweg
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		be.bastelstu.josh.ps
 */
class PackageServerPackagePermissionOverviewPage extends \wcf\page\AbstractPage {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.permissionOverview';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.packageServer.canManagePackages');
	
	/**
	 * all permission items
	 * @var array<mixed>
	 */
	public $items = array();
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read first all general permissions
		$sql = "(
				SELECT	packageIdentifier, permissions, 'general' AS type
				FROM wcf". WCF_N ."_packageserver_package_permission_general
			)
			UNION ALL
			(
				SELECT	packageIdentifier, permissions, 'user' AS type
				FROM wcf". WCF_N ."_packageserver_package_to_user
			)
			UNION ALL
			(
				SELECT	packageIdentifier, permissions, 'group' AS type
				FROM wcf". WCF_N ."_packageserver_package_to_group
			)";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute();
		while ($row = $stmt->fetchArray()) {
			$this->items[] = $row;
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'items' => $this->items
		));
	}
}
