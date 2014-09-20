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
class PackageServerPackageUserPermissionAddForm extends AbstractForm {
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.addUserPermission';
	
	public $neededPermissions = array('admin.packageServer.canManagePackages');
	
	public $packageIdentifer = '';
	
	public $permission = '';
	
	public $username = '';
	
	public $user = array();
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (isset($_GET['package'])) {
			$this->packageIdentifer = $_GET['package'];
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['package'])) {
			$this->packageIdentifer = $_POST['package'];
		}
		
		if (isset($_POST['permission'])) {
			$this->permission = $_POST['permission'];
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
		
		try {
			foreach ($this->user as $username => $object) {
				if ($object->getObjectID() == 0) {
					throw new \wcf\system\exception\UserInputException('username', $username);
				}
			}
		}
		catch (\wcf\system\exception\UserInputException $e) {
			// remove all invalid objects for the template
			foreach ($this->user as $username => $object) {
				if ($object->getObjectID() == 0) {
					unset($this->user[$username]);
				}
			}
			
			// throw up :)
			throw $e;
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
		
		$this->packageIdentifer = $this->permission = "";
		
		$this->user = array();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'permission' => $this->permission,
			'package' => $this->packageIdentifer,
			'user' => $this->user
		));
	}
}
