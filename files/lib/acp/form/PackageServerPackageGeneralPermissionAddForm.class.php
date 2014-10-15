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
class PackageServerPackageGeneralPermissionAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.packageserver.package.addGeneralPermission';
	
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
	 * @see	\wcf\page\IPage::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['packageIdentifier'])) $this->packageIdentifier = $_POST['packageIdentifier'];
		if (isset($_POST['permissionString'])) $this->permissionString = $_POST['permissionString'];
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
		
		$sql = "SELECT COUNT(*)
			FROM wcf". WCF_N ."_packageserver_package_permission_general
			WHERE packageIdentifier = ?";
			
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute(array($this->packageIdentifier));
		
		if ($stmt->fetchColumn()) {
			throw new UserInputException('packageIdentifier', 'existing');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		$sql = "INSERT INTO wcf". WCF_N ."_packageserver_package_permission_general
				(packageIdentifier, permissions)
			VALUES
				(?, ?)";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute(array($this->packageIdentifier, $this->permissionString));
		
		// regenerate auth file @TODO, better solution work in progress
		PackageServerUtil::generateAuthFile();
		
		$this->saved();
		
		$this->packageIdentifier = $this->permissionString = "";
	
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'permissionString' => $this->permissionString,
			'packageIdentifier' => $this->packageIdentifier,
			'action' => 'add'
		));
	}
}
