<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * A form for add package permissions
 *
 * @author	Tim Düsterhus, Joshua Rüsweg
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	be.bastelstu.josh.ps
 */
class PackageServerPackageGroupPermissionAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.addGroupPermission';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.packageServer.canManagePackages');
	
	/**
	 * Package identifier
	 * @var	string
	 */
	public $packageIdentifier = '';
	
	/**
	 * Permission string
	 * @var	string
	 */
	public $permissionString = '';
	
	/**
	 * List of group IDs
	 * @var	array
	 */
	public $groupIDs = array();
	
	/**
	 * Instance of UserGroupList
	 * @var	\wcf\data\user\group\UserGroupList
	 */
	public $groupList = null;
	
	/**
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['packageIdentifier'])) $this->packageIdentifier = \wcf\util\StringUtil::trim($_POST['packageIdentifier']);
		if (isset($_POST['permissionString'])) $this->permissionString = \wcf\util\StringUtil::trim($_POST['permissionString']);
		if (isset($_POST['groupIDs'])) $this->groupIDs = \wcf\util\ArrayUtil::toIntegerArray($_POST['groupIDs']);
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		$this->groupList = new \wcf\data\user\group\UserGroupList();
		$this->groupList->getConditionBuilder()->add('groupType NOT IN (?)', array(\wcf\data\user\group\UserGroup::EVERYONE));
		
		$this->groupList->readObjects();
		
		parent::readData();
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->packageIdentifier)) {
			throw new UserInputException('packageIdentifier');
		}
		
		if (!\wcf\data\package\Package::isValidPackageName($this->packageIdentifier)) {
			throw new UserInputException('packageIdentifier', 'notValid');
		}
		
		if (empty($this->permissionString)) {
			throw new UserInputException('permissionString');
		}
		
		if (empty($this->groupIDs)) {
			throw new UserInputException('groupIDs');
		}
		
		foreach ($this->groupIDs as $groupID) {
			if ($this->groupList->search($groupID) === null) {
				throw new UserInputException('groupIDs', 'notValid');
			}
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		$sql = "INSERT INTO wcf". WCF_N ."_packageserver_package_to_group
				(packageIdentifier, permissions, groupID)
			VALUES
				(?, ?, ?)";
		$stmt = WCF::getDB()->prepareStatement($sql);
		foreach ($this->groupIDs as $groupID) {
			$stmt->execute(array($this->packageIdentifier, $this->permissionString, $groupID));
		}
		
		// regenerate auth file @TODO, better solution work in progress
		PackageServerUtil::generateAuthFile();
		
		$this->saved();
		
		$this->packageIdentifier = $this->permissionString = "";
		$this->groupIDs = array();
		
		// show success
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'permissionString' => $this->permissionString,
			'packageIdentifier' => $this->packageIdentifier,
			'groupIDs' => $this->groupIDs,
			'availableGroups' => $this->groupList,
			'action' => 'add'
		));
	}
}
