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
class PackageGroupPermissionAddForm extends AbstractForm {
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.addgrouppermission';
	
	public $neededPermissions = array('admin.packageServer.canAddPermissions');
	
	public $packageIdentifer = ''; 
	
	public $permission = ''; 
	
	public $groupIDs = '';
	
	public $groups = array(); 
	
	public $groupList = array(); 
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (isset($_GET['package'])) {
			$this->packageIdentifer = $_GET['package']; 
		}
		
		$this->groupList = new \wcf\data\user\group\UserGroupList(); 
	}
	
	/**
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['permission'])) {
			$this->permission = $_POST['permission']; 
		}
		
		if (isset($_POST['package'])) {
			$this->packageIdentifer = $_POST['package']; 
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
		
		if (empty($this->groups)) {
			throw new \wcf\system\exception\UserInputException('group');
		}
		
		foreach ($this->groups as $group) {
			if ($group->getObjectID() == 0) {
				throw new \wcf\system\exception\UserInputException('group');
			}
		} 
		
		if (empty($this->packageIdentifer)) {
			throw new \wcf\system\exception\UserInputException('package');
		}
		
		if (empty($this->permission)) {
			throw new \wcf\system\exception\UserInputException('permission');
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
		
		$this->packageIdentifer = $this->permission = ""; 
		
		$this->groups = array(); 
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	public function assignVariables() {
		parent::assignVariables();
		
		$this->groupList->readObjects(); 
		
		WCF::getTPL()->assign(array(
		    'permission' => $this->permission,
		    'package' => $this->packageIdentifer, 
		    'group' => $this->groups, 
		    'groups' => $this->groupList->getObjects()
		)); 
	}
}
