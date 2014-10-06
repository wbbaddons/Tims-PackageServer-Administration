<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * A form for add package permissions
 *
 * @author		Tim Düsterhus, Joshua Rüsweg
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		be.bastelstu.josh.ps
 */
class PackageServerPackageUserPermissionAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.addUserPermission';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.packageServer.canManagePackages');
	
	public $packageIdentifier = '';
	public $permissionString = '';
	public $usernames = array();
	public $userList = null;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['packageIdentifier'])) $this->packageIdentifier = \wcf\util\StringUtil::trim($_REQUEST['packageIdentifier']);
	}
	
	/**
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['permissionString'])) $this->permissionString = \wcf\util\StringUtil::trim($_POST['permissionString']);
		if (isset($_POST['usernames'])) $this->usernames = array_filter(\wcf\util\ArrayUtil::trim(explode(',', $_POST['usernames'])));
	}
	
	public function readData() {
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
		
		$this->userList = new \wcf\data\user\UserList();
		$this->userList->getConditionBuilder()->add('username IN (?)', array($this->usernames));
		$this->userList->readObjects();
		
		$tmp = array();
		foreach ($this->userList as $user) $tmp[] = mb_strtolower($user->username);
		$difference = array_diff(array_map('mb_strtolower', $this->usernames), $tmp);
		
		if (!empty($difference)) {
			WCF::getTPL()->assign(array(
				'unknownUsers' => $difference
			));
			
			throw new UserInputException('usernames', 'notFound');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		$sql = "INSERT INTO wcf". WCF_N ."_packageserver_package_to_user
				(packageIdentifier, permissions, userID)
			VALUES
				(?, ?, ?)";
		$stmt = WCF::getDB()->prepareStatement($sql);
		foreach ($this->userList as $user) {
			$stmt->execute(array($this->packageIdentifier, $this->permissionString, $user->userID));
		}
		
		// regenerate auth file @TODO, better solution work in progress
		PackageServerUtil::generateAuthFile();
		
		$this->saved();
		
		$this->packageIdentifier = $this->permissionString = "";
		$this->usernames = array();
		
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
			'usernames' => $this->usernames,
			'action' => 'add'
		));
	}
}
