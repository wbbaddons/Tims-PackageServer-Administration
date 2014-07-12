<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\WCF; 
use wcf\util\PackageServerUtil; 

/**
 * A form for add package permissions
 * 
 * @author		Joshua Rüsweg
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		be.bastelstu.josh.ps
 */
class PackagePermissionAddForm extends AbstractForm {
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.addgrouppermission';
	
	public $neededPermissions = array('admin.packageServer.canAddPermissions');
	
	public $packageIdentifer = ''; 
	
	public $permission = ''; 
	
	public $groupIDs = '';
	
	public $groups = array(); 
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (isset($_GET['packageIdentifer'])) {
			$this->packageIdentifer = $_GET['packageIdentifer']; 
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['packageIdentifer'])) {
			$this->packageIdentifer = $_POST['packageIdentifer']; 
		}
		
		if (isset($_POST['groups'])) {
			$this->groupIDs = $_POST['groups']; 
		}
		
		foreach ($this->groupIDs as $group) {
			$this->groups[] = new \wcf\data\user\group\UserGroup($group);
		}
	}
	
	public function validate() {
		parent::validate();
		
		foreach ($this->groups as $group) {
			if ($group->getObjectID() == 0) {
				throw new \wcf\system\exception\UserInputException('group');
			}
		} 
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$sql = "INSERT INTO wcf". WCF_N ."_packageserver_package_to_group
				(packageIdentifier, permissions, groupID) 
			VALUES
				(?, ?, ?)"; 
		$stmt = WCF::getDB()->prepareStatement($sql); 
		foreach ($this->groups as $group) $stmt->execute(array($this->packageIdentifer, $this->permission, $group));
		
		// regenerate auth file @TODO, better solution work in progress
		PackageServerUtil::generateAuthFile(); 
		
		$this->saved();
	}
	
	public function saved() {
		parent::saved();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
}
