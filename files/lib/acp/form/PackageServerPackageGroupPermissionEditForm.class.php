<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\PackageServerUtil;

/**
 * Shows the package group permission edit form.
 *
 * @author	Maximilian Mader
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	be.bastelstu.josh.ps
 */
class PackageServerPackageGroupPermissionEditForm extends PackageServerPackageGroupPermissionAddForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver';
	
	/**
	 * Group id
	 * @var	integer
	 */
	public $groupID = 0;
	
	/**
	 * Benefited user group
	 * @var	\wcf\data\user\group\UserGroup
	 */
	public $userGroup = null;
	
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
		if (isset($_REQUEST['groupID'])) $this->groupID = intval($_REQUEST['groupID']);
		
		$this->userGroup = new \wcf\data\user\group\UserGroup($this->groupID);
		
		if (!$this->userGroup->groupID) {
			throw new IllegalLinkException();
		}
		
		$sql = "SELECT	*
			FROM	wcf". WCF_N ."_packageserver_package_to_group
			WHERE		packageIdentifier = ?
				AND	groupID = ?";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute(array($this->packageIdentifier, $this->userGroup->groupID));
		
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
		
		$sql = "UPDATE	wcf". WCF_N ."_packageserver_package_to_group
			SET	permissionString = ?
			WHERE		packageIdentifier = ?
				AND	groupID = ?";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute(array($this->permissionString, $this->packageIdentifier, $this->userGroup->groupID));
		
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
			'group' => $this->userGroup
		));
	}
}
