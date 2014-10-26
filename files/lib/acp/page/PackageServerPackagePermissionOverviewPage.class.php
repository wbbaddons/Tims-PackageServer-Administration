<?php
namespace wcf\acp\page;
use wcf\data\user\group\UserGroup;
use wcf\system\WCF;

/**
 * Represents a list of all permissions
 *
 * @author	Tim Düsterhus, Maximilian Mader, Joshua Rüsweg
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	be.bastelstu.josh.ps
 */
class PackageServerPackagePermissionOverviewPage extends \wcf\page\SortablePage {
	/**
	 * @see	wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.permissionOverview';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.packageServer.canManagePackages');
	
	/**
	* @see	\wcf\page\SortablePage::$defaultSortField
	*/
	public $defaultSortField = 'packageIdentifier';
	
	/**
	 * @see	\wcf\page\SortablePage::$validSortFields
	 */
	public $validSortFields = array(
		'packageIdentifier',
		'permissions',
		'beneficiary',
		'type'
	);
	
	/**
	* @see	\wcf\page\MultipleLinkPage::$itemsPerPage
	*/
	public $itemsPerPage = 50;
	
	/**
	 * List of permissions
	 * @var	array
	 */
	public $permissions = array();
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		return;
	}
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::readObjects()
	 */
	public function readObjects() {
		// Read all permissions in a single query
		$sql = "(
				SELECT	packageIdentifier, permissionString, NULL AS beneficiaryID, NULL AS beneficiary, 'general' AS type
				FROM	wcf".WCF_N."_packageserver_package_permission_general
			)
			UNION ALL
			(
				SELECT		perm_table.packageIdentifier, perm_table.permissionString, perm_table.userID AS beneficiaryID, user_table.username AS beneficiary, 'user' AS type
				FROM		wcf".WCF_N."_packageserver_package_to_user perm_table
				LEFT JOIN	wcf".WCF_N."_user user_table
				ON		(user_table.userID = perm_table.userID)
			)
			UNION ALL
			(
				SELECT		perm_table.packageIdentifier, perm_table.permissionString, perm_table.groupID AS beneficiaryID, group_table.groupName AS beneficiary, 'group' AS type
				FROM		wcf".WCF_N."_packageserver_package_to_group perm_table
				LEFT JOIN	wcf".WCF_N."_user_group group_table ON (group_table.groupID = perm_table.groupID)
			)
			ORDER BY ".$this->sortField." ".$this->sortOrder;
			
		$stmt = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$stmt->execute();
		
		while ($row = $stmt->fetchArray()) {
			$this->permissions[] = $row;
		}
	}
	
	public function countItems() {
		// Count every permission and sum them up
		$sql = "SELECT (
				SELECT COUNT(*)
				FROM wcf".WCF_N."_packageserver_package_permission_general
			) + (
				SELECT COUNT(*)
				FROM wcf".WCF_N."_packageserver_package_to_user
			) + (
				SELECT COUNT(*)
				FROM wcf".WCF_N."_packageserver_package_to_group
			)";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute();
		
		return $stmt->fetchColumn();
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'permissions' => $this->permissions
		));
	}
}
