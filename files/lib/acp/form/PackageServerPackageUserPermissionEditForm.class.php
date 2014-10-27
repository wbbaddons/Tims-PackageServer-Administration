<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * Shows the package user permission edit form.
 *
 * @author	Maximilian Mader
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	be.bastelstu.josh.ps
 */
class PackageServerPackageUserPermissionEditForm extends PackageServerPackageUserPermissionAddForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver';
	
	/**
	 * User id
	 * @var	integer
	 */
	public $userID = 0;
	
	/**
	* Benefited user
	* @var	\wcf\data\user\User
	*/
	public $user = null;
	
	/**
	 * The permission value
	 * @var	string
	 */
	public $permissionEntry = null;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['packageIdentifier'])) $this->packageIdentifier = \wcf\util\StringUtil::trim($_REQUEST['packageIdentifier']);
		if (isset($_REQUEST['userID'])) $this->userID = intval($_REQUEST['userID']);
		
		$this->user = new \wcf\data\user\User($this->userID);
		
		if (!$this->user->userID) {
			throw new IllegalLinkException();
		}
		
		$sql = "SELECT	*
			FROM	wcf". WCF_N ."_packageserver_package_to_user
			WHERE		packageIdentifier = ?
				AND	userID = ?";
		$stmt = WCF::getDB()->prepareStatement($sql, 1);
		$stmt->execute(array($this->packageIdentifier, $this->user->userID));
		
		$this->permissionEntry = $stmt->fetchArray();
		
		if (!$this->permissionEntry) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		AbstractForm::validate();
		
		if (empty($this->permissionString)) {
			throw new UserInputException('permissionString');
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (empty($_POST)) {
			$this->permissionString = $this->permissionEntry['permissionString'];
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		$sql = "UPDATE	wcf". WCF_N ."_packageserver_package_to_user
			SET	permissionString = ?
			WHERE		packageIdentifier = ?
				AND	userID = ?";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute(array($this->permissionString, $this->packageIdentifier, $this->user->userID));
		
		PackageServerUtil::generateAuthFile();
		
		$this->saved();
		
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
			'action' => 'edit',
			'user' => $this->user
		));
	}
}
