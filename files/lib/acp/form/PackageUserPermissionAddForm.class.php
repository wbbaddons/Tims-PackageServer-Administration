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
class PackageUserPermissionAddForm extends AbstractForm {
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.adduserpermission';
	
	public $neededPermissions = array('admin.packageServer.canAddPermissions');
	
	public $packageIdentifer = ''; 
	
	public $permission = ''; 
	
	public $username = '';
	
	public $user = array(); 
	
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
		
		if (isset($_POST['username'])) {
			$this->username = $_POST['username']; 
		}
		
		if (empty($this->username)) {
			$this->username = array(); 
		} else {
			$this->username = explode(',', $this->username);
			
			$this->username = array_map(function ($name) {
				return trim($name); 
			}, $this->username); 
		}
		
		foreach ($this->username as $user) {
			$this->user[$user] = \wcf\data\user\User::getUserByUsername($user);
		}
	}
	
	public function validate() {
		parent::validate();
		
		foreach ($this->user as $username => $object) {
			if ($object->getObjectID() == 0) {
				throw new \wcf\system\exception\UserInputException('username', $username);
			}
		} 
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$sql = "INSERT INTO wcf". WCF_N ."_packageserver_package_to_user
				(packageIdentifier, permissions, userID) 
			VALUES
				(?, ?, ?)"; 
		$stmt = WCF::getDB()->prepareStatement($sql); 
		foreach ($this->user as $user) $stmt->execute(array($this->packageIdentifer, $this->permission, $user->userID));
		
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
